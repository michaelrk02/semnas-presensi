<?php

class Presence extends CI_Controller {

    protected $user;
    protected $sso_id;
    protected $status = '<div></div>';

    public function __construct() {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('users_model', 'users');
        $this->load->model('sessions_model', 'sessions');
        $this->load->model('presence_model', 'presence');

        $this->check_status();
    }

    public function index() {
        redirect('presence/fill');
    }

    public function login() {
        if (isset($_SESSION['sso_id'])) {
            redirect('presence/fill');
        }

        $this->load->view('header', ['title' => 'Login']);
        $this->load->view('presence/login');
        $this->load->view('footer');
    }

    public function sso_login() {
        if (isset($_SESSION['sso_id'])) {
            redirect('presence/fill');
        }

        if (!empty($this->input->get('token'))) {
            $token = base64_decode($this->input->get('token'));
            $token = explode(':', $token);
            if (hash_hmac('sha256', $token[0], SEMNAS_SSO_APP_KEY) === $token[1]) {
                $token[0] = base64_decode($token[0]);
                $token[0] = json_decode($token[0], TRUE);
                if (time() < $token[0]['expired']) {
                    if (empty($token[0]['disallow'])) {
                        $_SESSION['sso_id'] = md5($token[0]['user_id']);
                        if (!$this->users->exists($_SESSION['sso_id'])) {
                            $this->users->add([
                                'sso_id' => $_SESSION['sso_id'],
                                'name' => $token[0]['name'],
                                'email' => $token[0]['email'],
                                'phone' => $token[0]['phone'],
                                'institution' => $token[0]['institution']
                            ]);
                        }
                        redirect('presence/fill');
                    } else {
                        die('Not allowed to login to this site');
                    }
                } else {
                    die('Invalid SSO token');
                }
            } else {
                die('Invalid SSO token');
            }
        } else {
            $sso = [];
            $sso['app_id'] = SEMNAS_SSO_APP_ID;
            $sso['timestamp'] = time();
            $sso['redirect_url'] = site_url(uri_string());
            $sso['redirect_param'] = 'token';
            $sso = base64_encode(json_encode($sso));
            $signature = hash_hmac('sha256', $sso, SEMNAS_SSO_APP_KEY);
            $token = base64_encode($sso.':'.$signature);
            $sso_url = SEMNAS_SSO_URL.'?sso='.urlencode($token);
            redirect($sso_url);
        }
    }

    public function logout() {
        unset($_SESSION['sso_id']);
        redirect('presence/login');
    }

    public function fill() {
        $this->check_login();

        $sessions = $this->sessions->get();

        $session_id = $this->input->get('session_id');
        $session = NULL;
        $presence = NULL;
        if (!empty($session_id)) {
            $session = $this->sessions->get($session_id);
            $presence = $this->presence->get($_SESSION['sso_id'], $session_id);

            if (isset($session)) {
                if (isset($presence)) {
                    if (!empty($this->input->post('submit'))) {
                        if (!empty($this->sessions->is_open($session))) {
                            $this->load->library('form_validation');

                            $this->form_validation->set_rules('imp_msg', 'Kesan dan pesan', 'max_length[2000]');
                            $this->form_validation->set_rules('suggestions', 'Saran ke depan', 'max_length[2000]');
                            $this->form_validation->set_rules('speaker_req', 'Usulan pembicara', 'max_length[2000]');

                            $presence['imp_msg'] = $this->input->post('imp_msg');
                            $presence['suggestions'] = $this->input->post('suggestions');
                            $presence['speaker_req'] = $this->input->post('speaker_req');

                            if ($this->form_validation->run()) {
                                $this->presence->set($_SESSION['sso_id'], $session_id, $presence);
                                $_SESSION['status'] = ['success', 'Form berhasil disimpan dan sudah terkirim ke server. Terima kasih atas partisipasi anda. Anda dapat menutup laman ini apabila anda tidak ingin mengubah tanggapan anda (namun tanggapan masih dapat diubah sampai waktu untuk sesi ini ditutup)'];
                            } else {
                                $_SESSION['status'] = ['danger', $this->form_validation->error_string()];
                            }
                        } else {
                            $_SESSION['status'] = ['danger', 'Maaf! Presensi untuk sesi ini ditutup'];
                        }
                    }
                    $this->check_status();
                } else {
                    if (!empty($this->input->get('create'))) {
                        if (!empty($this->sessions->is_open($session))) {
                            $this->presence->create($_SESSION['sso_id'], $session_id);
                            $_SESSION['status'] = ['success', 'Berhasil melakukan presensi. Silakan mengisi formulir tanggapan berikut'];
                            redirect(site_url('presence/fill').'?session_id='.urlencode($session_id).'&created=1');
                        } else {
                            $_SESSION['status'] = ['danger', 'Maaf! Presensi untuk sesi ini ditutup'];
                            redirect('presence/fill');
                        }
                    }
                }
            } else {
                die('Invalid request');
            }
        }

        $this->load->view('header', ['title' => 'Isi Presensi']);
        $this->load->view('presence/fill', [
            'status' => $this->status,
            'sessions' => $sessions,
            'user' => $this->user,
            'session' => $session,
            'session_is_scheduled' => $this->sessions->is_scheduled($session),
            'session_is_open' => $this->sessions->is_open($session),
            'presence' => $presence
        ]);
        $this->load->view('footer');
    }

    protected function check_login() {
        if (!isset($_SESSION['sso_id'])) {
            redirect('presence/login');
        }
        $this->user = $this->users->get($_SESSION['sso_id']);
        if (!isset($this->user)) {
            redirect('presence/logout');
        }
    }

    protected function check_status() {
        if (isset($_SESSION['status'])) {
            $this->status = '<div class="alert alert-'.$_SESSION['status'][0].'">'.$_SESSION['status'][1].'</div>';
            unset($_SESSION['status']);
        }
    }

}

