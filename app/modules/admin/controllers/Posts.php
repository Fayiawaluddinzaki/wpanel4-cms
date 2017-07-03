<?php

/**
 * @copyright Eliel de Paula <dev@elieldepaula.com.br>
 * @license http://wpanel.org/license
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pposts class
 * 
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 */
class Posts extends Authenticated_Controller
{

    /**
     * Class constructor.
     */
    function __construct()
    {
        $this->model_file = array('post', 'categoria', 'post_categoria');
        parent::__construct();
    }

    /**
     * List posts.
     */
    public function index()
    {
        $this->load->library('table');
        // Template da tabela
        $this->table->set_template(array('table_open' => '<table id="grid" class="table table-striped">'));
        $this->table->set_heading(
                '#', wpn_lang('col_title', 'Title'), wpn_lang('col_date', 'Date'), wpn_lang('col_status', 'Status'), wpn_lang('col_actions', 'Actions')
        );
        $query = $this->post->order_by('created_on', 'desc')->where('page', 0)->find_all();

        foreach ($query as $row)
        {
            $this->table->add_row(
                    $row->id, $row->title . '<br/><small>' . $this->widget->load('wpncategoryfrompost', array('post_id' => $row->id)) . '</small>', mdate('%d/%m/%Y', strtotime($row->created_on)), status_post($row->status),
                    // Ícones de ações
                    div(array('class' => 'btn-group btn-group-xs')) .
                    anchor('admin/posts/edit/' . $row->id, glyphicon('edit'), array('class' => 'btn btn-default')) .
                    '<button class="btn btn-default" onClick="return confirmar(\'' . site_url('admin/posts/delete/' . $row->id) . '\');">' . glyphicon('trash') . '</button>' .
                    div(null, true)
            );
        }
        $this->set_var('listagem', $this->table->generate());
        $this->render();
    }

    /**
     * Insert post.
     */
    public function add()
    {
        $this->form_validation->set_rules('title', 'Título', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            // Prepara a lista de categorias.
            $query = $this->categoria->find_all();
            $categorias = array();
            foreach ($query as $row)
            {
                $categorias[$row->id] = $row->title;
            }
            $this->set_var('categorias', $categorias);
            $this->render();
        } else
        {
            $data = array();
            $data['title'] = $this->input->post('title');
            $data['description'] = $this->input->post('description');
            $data['link'] = strtolower(url_title(convert_accented_characters($this->input->post('title')))) . '-' . time();
            $data['content'] = $this->input->post('content');
            $data['tags'] = $this->input->post('tags');
            $data['status'] = $this->input->post('status');
            $data['image'] = $this->wpanel->upload_media('capas');
            // Identifica se é uma página ou uma postagem
            // 0=post, 1=Página
            $data['page'] = '0';
            $new_post = $this->post->insert($data);
            if ($new_post)
            {
                // Salva o relacionamento das categorias
                foreach ($this->input->post('category_id') as $cat_id)
                {
                    $cat_save = array();
                    $cat_save['post_id'] = $new_post;
                    $cat_save['category_id'] = $cat_id;
                    $this->post_categoria->insert($cat_save);
                }
                $this->set_message('Postagem salva com sucesso!', 'success', 'admin/posts');
            } else
                $this->set_message('Erro ao salvar a postagem.', 'danger', 'admin/posts');
        }
    }

    /**
     * Edit an post.
     * 
     * @param int $id
     */
    public function edit($id = null)
    {
        $this->form_validation->set_rules('title', 'Título', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            if ($id == null)
                $this->set_message('Postagem inexistente!', 'info', 'admin/posts');
            // Prepara a lista de categorias.
            $query = $this->categoria->find_all();
            $categorias = array();
            foreach ($query as $row)
            {
                $categorias[$row->id] = $row->title;
            }
            // Prepara as categorias selecionadas.
            $query = $this->post_categoria->find_many_by('post_id', $id);
            $cat_select = array();
            foreach ($query as $x => $row)
            {
                $cat_select[$x] = $row->category_id;
            }
            $this->set_var('id', $id);
            $this->set_var('categorias', $categorias);
            $this->set_var('cat_select', $cat_select);
            $this->set_var('row', $this->post->find($id));
            $this->render();
        } else
        {
            $data = array();
            $data['title'] = $this->input->post('title');
            $data['description'] = $this->input->post('description');
            $data['link'] = strtolower(url_title(convert_accented_characters($this->input->post('title'))));
            $data['content'] = $this->input->post('content');
            $data['tags'] = $this->input->post('tags');
            $data['status'] = $this->input->post('status');
            // Identifica se é uma página ou uma postagem
            // 0=post, 1=Página
            $data['page'] = '0';
            if ($this->input->post('alterar_imagem') == '1')
            {
                $postagem = $this->post->find($id);
                $this->wpanel->remove_media('capas/' . $postagem->image);
                $data['image'] = $this->wpanel->upload_media('capas');
            }
            $upd_post = $this->post->update($id, $data);
            if ($upd_post)
            {
                // Apaga os relacionamentos anteriores.
                $this->post_categoria->delete_by_post($id);
                // Cadastra as alterações.
                foreach ($this->input->post('category_id') as $cat_id)
                {
                    $cat_save = array();
                    $cat_save['post_id'] = $id;
                    $cat_save['category_id'] = $cat_id;
                    $this->post_categoria->insert($cat_save);
                }
                $this->set_message('Postagem salva com sucesso!', 'success', 'admin/posts');
            } else
                $this->set_message('Erro ao salvar a postagem.', 'danger', 'admin/posts');
        }
    }

    /**
     * Delete an post.
     * 
     * @param int $id
     */
    public function delete($id = null)
    {
        if ($id == null)
            $this->set_message('Postagem inexistente!', 'info', 'admin/posts');
        $postagem = $this->post->find($id);
        $this->wpanel->remove_media('capas/' . $postagem->image);
        if ($this->post->delete($id))
            $this->set_message('Postagem excluída com sucesso!', 'success', 'admin/posts');
        else
            $this->set_message('Erro ao excluir a postagem', 'danger', 'admin/posts');
    }

}
