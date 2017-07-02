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
 * Backup class generate an sql backup file from the mysql database.
 * 
 * @author Eliel de Paula <dev@elieldepaula.com.br>
 * @since v1.0.0
 */
class Backups extends Authenticated_Controller
{

    /**
     * Class constructor.
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Index forbidden.
     */
    public function index()
    {
        redirect('admin');
    }

    /**
     * Execute the backup proccess.
     */
    public function execute()
    {

        $filename = 'wpanel-backup-' . date('d-m-Y-H-i-s') . '.sql';

        // Load the DB utility class
        $this->load->dbutil();

        // Backup your entire database and assign it to a variable
        $prefs = array(
            'format' => 'zip',
            'filename' => $filename,
            'add_drop' => TRUE,
            'add_insert' => TRUE,
            'newline' => "\n"
        );

        $backup = & $this->dbutil->backup($prefs);

        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        force_download($filename . '.zip', $backup);
    }

}
