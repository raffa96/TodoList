<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model {

	function validate_credentials($email, $password) {
		$this->db->select('*');
		$this->db->from('access');
		$this->db->where('email', $email);
		$this->db->where('password', sha1($password));

		$query = $this->db->get();

		if ($query && $query->num_rows() == 1) {
			return $query->result()[0];
		} else {
			return null;
		}
	}

	// CRUD
	// Tables -> Objects

	// C - Create
	function c_object($table, $data) {
		$this->db->insert($table, $data);
	}

	// R - Read
	function ra_object($table, $column, $id) {
		$this->db->select('*');
		$this->db->from($table);
		if ($column && $id) {
			$this->db->where($column, $id);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$data[] = $row;
			}

			return $data;
		}
	}

	function r_object($table, $id, $access) {
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where('id', $id);
		if ($access) {
			$this->db->where('idAccess', $access);
		}

		$query = $this->db->get()->result();

		if ($query) {
			return $query[0];
		} else {
			return null;
		}
	}

	// U - Update
	function u_object($table, $id, $data, $access) {
		$this->db->where('id', $id);
		if ($access) {
			$this->db->where('idAccess', $access);
		}
		$this->db->update($table, $data);
	}

	// D - Delete
	function d_object($table, $id, $access) {
		$this->db->where('id', $id);
		if ($access) {
			$this->db->where('idAccess', $access);
		}
		
		$this->db->delete($table);
	}
}