<?php

namespace App\Controllers\Portal_controllers;
use App\Controllers\BaseController;
use App\Libraries\Portal_breadcrumb;

class Ins_keyboards extends BaseController {
    private $breadcrumb;

    public function index() {
        return $this->create_view('portal_views/ins_keyboards', $this->load_data());
    }//end index function

    private function load_data(){
        $this->breadcrumb = new Portal_breadcrumb();

        $data = array();
        $data['section_name'] = 'Teclados';

        $keyboards_table = new \App\Models\Tabla_teclado();
        $data['keyboards'] = $keyboards_table->get_portal_keyboards();

        //Breadcrumb
        $this->breadcrumb->add_breadcrumb('Inicio', '/');
        $this->breadcrumb->add_breadcrumb('Instrumentos', 'instrumentos/teclados');
        $this->breadcrumb->add_breadcrumb('Teclados', 'instrumentos/teclados');
        $data['breadcrumb'] = $this->breadcrumb->generate_breadcrumb();

        return $data;
    }//end load_data function

    private function create_view($view_name = '', $content = array()) {
        $content['menu'] = generate_portal_navbar(PORTAL_INS_KEYBOARDS_TASK);
        return view($view_name, $content);
    }//end create view function
}//end Ins_keyboards class
