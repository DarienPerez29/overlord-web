<?php

namespace App\Controllers\Panel_controllers;
use App\Controllers\BaseController;
use App\Libraries\Permissions;
use App\Libraries\Panel_breadcrumb;

class Dashboard extends BaseController {
    private $is_allowed = TRUE;
    private $breadcrumb;

    public function __construct() {
        $session = session();
        $this->breadcrumb = new Panel_breadcrumb();

        if(!Permissions::is_role_allowed(DASHBOARD_TASK, (isset($session->id_rol) ? $session->id_rol : 0))) {
            $this->is_allowed = FALSE;
        }//end if role not allowed
    }//end __construct function

    public function index() {
        if($this->is_allowed){
            return $this->create_view('panel_views/index', $this->load_data());
        }//end if not allowed
        else {
            return redirect()->to(route_to('login'));
        }//end else not allowed
    }//end index function

    private function load_data() {
        $data = array();
        //Session elements
        $session = session();
        $data['user_name'] = $session->user_name;
        $data['user_full_name'] = $session->user_full_name;
        $data['user_img'] = $session->user_img;
        $data['user_sex'] = $session->user_sex;
        $data['user_role'] = $session->user_rol;
        $data['user_img'] = $session->user_img;

        $data['section_name'] = 'Dashboard';

        $guitars_table = new \App\Models\Tabla_guitarra();
        $data['guitars_quantity'] = count($guitars_table->get_guitars_quantity());
        $drums_table = new \App\Models\Tabla_bateria();
        $data['drums_quantity'] = count($drums_table->get_drums_quantity());
        $keyboards_table = new \App\Models\Tabla_teclado();
        $data['keyboards_quantity'] = count($keyboards_table->get_keyboards_quantity());
        $monitors_table = new \App\Models\Tabla_monitor();
        $data['monitors_quantity'] = count($monitors_table->get_monitors_quantity());
        $users_table = new \App\Models\Tabla_usuario();
        $data['users_quantity'] = count($users_table->get_users_quantity());

        //Breadcrumb
        $this->breadcrumb->add_breadcrumb('Dashboard', 'panel/dashboard');
        $data['breadcrumb'] = $this->breadcrumb->generate_breadcrumb();

        return $data;
    }//end load_data function

    private function create_view($view_name = '', $content = array()){
        $content['menu'] = generate_nav_menu(DASHBOARD_TASK, session()->id_rol);
        return view($view_name, $content);
    }//end create_view function
}//end Dashboard class

