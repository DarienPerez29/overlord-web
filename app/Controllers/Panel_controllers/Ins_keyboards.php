<?php

namespace App\Controllers\Panel_controllers;
use App\Controllers\BaseController;
use App\Libraries\Permissions;
use App\Libraries\Panel_breadcrumb;

class Ins_keyboards extends BaseController {
    private $is_allowed = TRUE;
    private $breadcrumb;

    public function __construct() {
        $session = session();
        $this->breadcrumb = new Panel_breadcrumb();

        if(!Permissions::is_role_allowed(INS_KEYBOARDS_TASK, (isset($session->id_rol) ? $session->id_rol : 0))){
            $this->is_allowed = FALSE;
        }//end if role not allowed
    }//end __construct function

    public function index() {
        if($this->is_allowed){
            return $this->create_view('panel_views/ins_keyboards', $this->load_data());
        }//end if not allowed
        else {
            create_user_message('No cuentas con los permisos suficientes para acceder a esta sección...', 'warning');
            return redirect()->to(route_to('panel/dashboard'));
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

        $data['section_name'] = 'Teclados';

        //Breadcrumb
        $this->breadcrumb->add_breadcrumb('Dashboard', 'panel/dashboard');
        $this->breadcrumb->add_breadcrumb('Instrumentos', 'panel/teclados');
        $this->breadcrumb->add_breadcrumb('Teclados', 'panel/teclados');
        $data['breadcrumb'] = $this->breadcrumb->generate_breadcrumb();

        // ==============
        // TECLADOS TODOS
        // ==============
        $keyboard_table = new \App\Models\Tabla_teclado;
        $data['keyboards_all'] = $keyboard_table->get_datatable_keyboards();

        return $data;
    }//end load_data function

    private function create_view($view_name = '', $content = array()){
        $content['menu'] = generate_nav_menu(INS_KEYBOARDS_TASK, session()->id_rol);
        return view($view_name, $content);
    }//end create_view function

    public function delete_keyboard($id_keyboard = 0){
        $keyboard_table = new \App\Models\Tabla_teclado();
        if($keyboard_table->find($id_keyboard) != NULL){
            $keyboard_in_db = $keyboard_table->find($id_keyboard);
            if($keyboard_table->delete($id_keyboard)){
                $this->delete_file($keyboard_in_db->imagen);
                create_user_message('El teclado <b>' . $keyboard_in_db->marca .' '. $keyboard_in_db->modelo .' '. $keyboard_in_db->acabado_color . '</b> ha sido eliminado', 'success');
                return redirect()->to(route_to('panel/teclados'));
            }// end if eliminación de usuario
            else{
                create_user_message('No se ha podido eliminar el teclado, intente de nuevo...', 'error');
                return redirect()->to(route_to('panel/teclados'));
            }// end else eliminación de usuario
        }// end if existe usuario
        else{
            create_user_message('El teclado a eliminar no existe...', 'error');
            return redirect()->to(route_to('panel/teclados'));
        }
    }// end delete_keyboard function

    private function delete_file($file = NULL) {
        if(file_exists('img/products/' . $file) && $file != 'k00.jpg'){
            unlink('img/products/' . $file);
        }//end if file exist
    }// end delete_file function
}//end Dashboard class