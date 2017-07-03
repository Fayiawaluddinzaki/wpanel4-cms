<?php

/**
 * @copyright Eliel de Paula <dev@elieldepaula.com.br>
 * @license http://wpanel.org/license
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Classe Users
 *
 * Esta classe contém os métodos básicos para gerenciamento de usuários
 * no site.
 * 
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
class Users extends MY_Controller
{

    /**
     * Class constructor.
     *
     * @return void
     */
    function __construct()
    {

        /**
         * Here are some options provided by the MY_Controller class, you
         * can adjust as you need to your project.
         */
        /**
         * Enable the CodeIgniter Profile.
         */
//        $this->show_profiler = TRUE;

        /**
         * Set the 'col' number of the mosaic views.
         */
        // $this->wpn_cols_mosaic = 3;

        /**
         * Set the default post view: list (default) or mosaic.
         */
        // $this->wpn_posts_view = 'mosaic';

        parent::__construct();
        $this->wpanel->check_setup();

        if ($this->auth->is_logged() and $this->auth->is_admin())
        {
            $this->set_message('Esta área é destinada somente a usuários comuns.', 'info', '');
            $this->auth->logout();
        }
    }

    /**
     * User dashboard.
     * 
     * @return void
     */
    public function index()
    {
        // Check the login.
        if (!$this->auth->is_logged())
            redirect('users/login');
        $this->wpanel->set_meta_title('Área de usuários');
        $this->render();
    }

    /**
     * This is an stant-alone account register page.
     * 
     * @return  void
     */
    public function register()
    {
        $this->form_validation->set_rules('name', 'Nome', 'required');
        $this->form_validation->set_rules('email', 'E-Mail', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Senha', 'required');
        $this->form_validation->set_rules('confpass', 'Confirmação de senha', 'required|matches[password]');
        if ($this->form_validation->run() == FALSE)
        {
            $this->wpanel->set_meta_title('Cadastro de usuários');
            $this->render();
        } else
        {
            $extra_data = array(
                'name' => $this->input->post('name'),
                'avatar' => '',
                'skin' => ''
            );
            if ($this->auth->register($this->input->post('email'), $this->input->post('password'), 'user', $extra_data))
                redirect('users/registerok');
            else
                $this->set_message('Sua conta não pode ser criada, verifique seus dados e tente novamente.', 'danger', 'users/register');
        }
    }

    /**
     * Simple register success information.
     * 
     * @return void
     */
    public function registerok()
    {
        $this->wpanel->set_meta_title('Cadastro de usuários');
        $this->render();
    }

    /**
     * This method activate an account by token.
     * 
     * @author Eliel de Paula <dev@elieldepaula.com.br>
     * @param string $token
     */
    public function activate($token)
    {
        try
        {
            $this->auth->activate_token_account($token);
            $this->wpanel->set_meta_title('Ativação de usuários');
            $this->render();
        } catch (Exception $e)
        {
            //TODO Verificar forma de tratar o erro com o usuário.
            $this->set_message('Sua conta não pode ser ativada, verifique seus dados e tente novamente.', 'danger', 'users');
        }
    }

    /**
     * Recovery the password of the account.
     * 
     * @author Eliel de Paula <dev@elieldepaula.com.br>
     * @param string $token
     */
    public function recovery($token = NULL)
    {
        $this->form_validation->set_rules('email', 'E-Mail', 'required|valid_email');
        if ($this->form_validation->run() == FALSE)
        {

            if ($token)
            {
                if ($this->auth->recovery($token))
                    $this->view('users/recovery_done')->render();
                else
                    $this->set_message('Link de confirmação inválido.', 'danger', 'users/recovery');
            } else
                $this->view('users/recovery_form')->render();
        } else
        {

            $email = $this->input->post('email');
            if ($this->auth->email_exists($email) == FALSE)
                $this->set_message('O e-mail informado não existe em nosso cadastro.', 'danger', 'users/recovery');

            if ($this->auth->send_recovery($email))
                $this->view('users/recovery_sent')->render();
            else
                $this->set_message('Houve um erro inesperado e sua senha não pode ser redefinida. Tente novamente mais tarde.', 'danger', 'users/recovery');
        }
    }

    /**
     * Profile user account page.
     * 
     * @author Eliel de Paula <dev@elieldepaula.com.br>
     * @return void
     */
    public function profile()
    {
        // Verifica o login.
        if (!$this->auth->is_logged())
            redirect('users/login');

        // Recupera os dados do usuário.
        $query = $this->auth->account();
        $profile = $this->auth->profile();

        $this->form_validation->set_rules('name', 'Nome', 'required');
        $this->form_validation->set_rules('email', 'E-Mail', 'required|valid_email');
        if ($this->input->post('alt_password'))
            $this->form_validation->set_rules('password', 'Senha', 'required');
        if ($this->form_validation->run() == FALSE)
        {

            $this->wpanel->set_meta_title('Área de usuários');
            $this->set_var('account', $query);
            $this->set_var('profile', $profile);
            $this->render();
        } else
        {

            // Salva os dados adicionais na coluna 'extra_data' na tabela accounts.
            $profile->name = $this->input->post('name');
            //$profile->demo = $this->input->post('demo');

            if ($this->auth->update($query->id, $this->input->post('email'), 'user', $profile))
            {
                // Verifica se deve alterar a senha do usuário.
                if ($this->input->post('alt_password'))
                    $this->auth->change_password($query->id, $this->input->post('password'));
                $this->set_message('Seus dados foram alterados com sucesso.', 'success', 'users/profile');
            } else
                $this->set_message('Houve um erro inesperado e seus dados não puderam ser alterados. Tente novamente mais tarde.', 'danger', 'users/profile');
        }
    }

    /**
     * Login and register page, also have links to login with facebook and G+.
     * 
     * @return mixed
     */
    public function login()
    {
        $this->form_validation->set_rules('email', wpn_lang('input_email', 'Email'), 'required|valid_email');
        $this->form_validation->set_rules('password', wpn_lang('input_password', 'Password'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->wpanel->set_meta_title('Login de usuários');
            $this->render();
        } else
        {
            if ($this->auth->login($this->input->post('email'), $this->input->post('password')))
                return redirect('users');
            else
                $this->set_message('Seu login falhou, tente novamente.', 'danger', 'users/login');
        }
    }

    /**
     * Logout the user.
     * 
     * @return void
     */
    public function logout()
    {
        $this->facebook->destroySession();
        $this->auth->logout();
        redirect();
    }

}
