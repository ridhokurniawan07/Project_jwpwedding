<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Beranda extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('katalog_model');
        $this->load->model('pesanan_model');
        $this->load->model('profile_model');
        $this->load->helper('text');
    }

    public function index()
    {
        $data = array(
            'title' => 'Jewepe Wedding Organizer',
            'page' => 'landing/beranda',
            'getAllKatalog' => $this->katalog_model->get_all_katalog()->result(),
            'getDataWeb' => $this->profile_model->getProfile('1')->row(),
        );
        $this->load->view('landing/template/sites', $data);
    }

    public function detail()
    {
        if ($this->input->get('id')) {
            $cek_data = $this->katalog_model->get_katalog_by_id($this->input->get('id'))->num_rows();

            if ($cek_data > 0) {
                $data = array(
                    'title' => 'Jewepe Wedding Organizer',
                    'page' => 'landing/detail',
                    'katalog' => $this->katalog_model->get_katalog_by_id($this->input->get('id'))->row()
                );
                $this->load->view('landing/template/sites', $data);
            } else {
                redirect('/');
            }
        } else {
            redirect('/');
        }
    }

    public function pesan()
    {
        if ($this->input->post()) {

            $post = $this->input->post();
            $cek_data = $this->pesanan_model->cek_data_pesanan($post['id'], $post['email_pemesan'], $post['tanggal'])->num_rows();

            if ($cek_data == 0) {

                $data = array(
                    'id_katalog' => $post['id'],
                    'nama_pemesan' => $post['nama_pemesan'],
                    'email_pemesan' => $post['email_pemesan'],
                    'tanggal' => $post['tanggal'],
                    'status' => 'requested',
                );

                $insert = $this->pesanan_model->insert($data);

                if ($insert) {
                    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success </strong>Terimakasih, permintaan pesanan anda kami terima, Silahkan tunggu konfirmasi kami melalui email!<i class="remove ti-close" data-dismiss="alert"></i></div>');
                    redirect('Beranda/detail?id=' . $post['id']);
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>UPSS! </strong>Maaf, Permintaan pesanan gagal! <i class="remove ti-close" data-dismiss="alert"></i></div>');
                    redirect('Beranda/detail?id=' . $post['id']);
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>UPSS! </strong>Maaf, Paket dan Tanggal pernikahan sudah anda pesan sebelumnya, silahkan tunggu konfirmasi dari kami! <i class="remove ti-close" data-dismiss="alert"></i></div>');
                redirect('Beranda/detail?id=' . $post['id']);
            }
        } else {
            redirect('Beranda');
        }
    }
}
