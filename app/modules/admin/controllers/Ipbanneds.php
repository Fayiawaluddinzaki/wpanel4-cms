<?php

/**
 * WPanel CMS
 *
 * An open source Content Manager System for websites and systems using CodeIgniter.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2008 - 2017, Eliel de Paula.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     WpanelCms
 * @author      Eliel de Paula <dev@elieldepaula.com.br>
 * @copyright   Copyright (c) 2008 - 2017, Eliel de Paula. (https://elieldepaula.com.br/)
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://wpanel.org
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Esta é a classe do módulo de administração Ipbanneds, ela foi
 * gerada automaticamente pela ferramenta Wpanel-GEN para a criação
 * de códigos padrão para o Wpanel CMS.
 *
 * @author      Eliel de Paula <dev@elieldepaula.com.br>
 * @since v1.0.0
 */
class Ipbanneds extends Authenticated_Controller
{

    /**
     * Class constructor
     */
    function __construct()
    {
        $this->model_file = 'ipban';
        parent::__construct();
    }

    /**
     * List IP's.
     */
    public function index()
    {
        $this->load->library('table');
        $this->table->set_template(array('table_open' => '<table id="grid" class="table table-striped">'));
        $this->table->set_heading('#', 'Endereço IP', 'Data', 'Ações');
        $query = $this->ipban->find_all();
        foreach ($query as $row)
        {
            $this->table->add_row(
                    $row->id, $row->ip_address, date('d/m/Y H:i:s', strtotime($row->created_on)),
                    // Ícones de ações
                    div(array('class' => 'btn-group btn-group-xs')) .
                    '<button class="btn btn-default" onClick="return confirmar(\'' . site_url('admin/ipbanneds/delete/' . $row->id) . '\');">' . glyphicon('trash') . '</button>' .
                    div(null, true)
            );
        }
        $this->set_var('listagem', $this->table->generate());
        $this->render();
    }

    /**
     * Insert an IP.
     */
    public function add()
    {
        $this->form_validation->set_rules('ip_address', 'Endereço IP', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->render();
        } else
        {
            $data = array();
            $data['ip_address'] = $this->input->post('ip_address');
            if ($this->ipban->insert($data))
                $this->set_message('Registro salvo com sucesso!', 'success', 'admin/ipbanneds');
            else
                $this->set_message('Erro ao salvar o registro.', 'danger', 'admin/ipbanneds');
        }
    }

    /**
     * Delete an IP.
     * 
     * @param int $id
     */
    public function delete($id = null)
    {
        if ($id == null)
            $this->set_message('Registro inexistente', 'info', 'admin/ipbanneds');
        if ($this->ipban->delete($id))
            $this->set_message('Registro excluído com sucesso!', 'success', 'admin/ipbanneds');
        else
            $this->set_message('Erro ao excluir o registro.', 'danger', 'admin/ipbanneds');
    }

}

// End of file modules/admin/controllers/Ipbanneds.php