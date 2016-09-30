<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kp_mdl extends CI_Model {
    public function get_items() {
        $result = $this->db->query("SELECT kp.id, kp.site_name, kp.search_machine, kp.region_promotion, kp.rate, kp.date_create, kp.data, users.name FROM kp INNER JOIN users ON users.id = kp.userid");

        return $result->result_array();
    }

    public function get_items_filter($filter_array) {
        # $filter_array = array(array('name_field' => 'value'),);

        $filter_sql = ' WHERE ';

        foreach ($filter_array as $count => $item) {
            $filter_sql .= "kp.{$item['name']} = {$item['value']}";

            if ($count != count($filter_array) - 1) {
                $filter_sql .= ' AND ';
            }
        }

        $result = $this->db->query("SELECT kp.id, kp.site_name, kp.search_machine, kp.region_promotion, kp.rate, kp.date_create, kp.data, users.name FROM kp INNER JOIN users ON users.id = kp.userid".$filter_sql);

        return $result->result_array();
    }

    public function get_item($id) {
        $result = $this->db->query("SELECT kp.*, yr.name as region_name, kd.text as description FROM kp as kp INNER JOIN yandex_regions as yr ON yr.id = kp.region_promotion LEFT OUTER JOIN kp_description
 as kd ON kd.kp_id = kp.id WHERE kp.id = {$id}");

        return $result->row_array();
    }

    public function remove($id) {
        $this->db->query("DELETE FROM kp WHERE id = {$id}");
        $this->db->query("DELETE FROM kp_description WHERE kp_id = {$id}");
    }

    public function edit($id, $data) {
        $this->db->query("DELETE FROM kp_description WHERE kp_id = {$id}");
        $this->db->query("DELETE FROM kp WHERE id = {$id}");
        $this->add($data);
    }

    public function add($data) {
        $date = time();
        $id = $this->db->query("SELECT MAX(id) as id FROM kp")->row();
        $id = $id->id + 1;

        $this->db->query("INSERT INTO `kp` (`id`, `site_name`, `search_machine`, `region_promotion`, `rate`, `date_create`, `userid`, `data`, `data_export`) VALUES ({$id}, '{$data['site_name']}', '{$data['search_machine']}', {$data['region_promotion']}, '{$data['rate']}', '{$date}', {$data['manager']}, '{$data['optional_data']}', '{$data['data_export']}')");
        $this->db->query("INSERT INTO `kp_description` (`kp_id`, `text`) VALUES ({$id}, '{$data['kp_description']}')");

        return $id;
    }

    public function get_all_regions() {
        $query = $this->db->query("SELECT * from yandex_regions");

        return $query->result_array();
    }

    public function get_managers() {
        $result = $this->db->query("SELECT id, name FROM users");

        return $result->result_array();
    }

    public function get_user($userid) {
        $result = $this->db->query("SELECT u.id, u.name, u.login, up.mobile_phone, up.additional_number FROM users as u INNER JOIN user_phone as up ON u.id = up.userid WHERE id = {$userid}");

        return $result->row_array();
    }
}