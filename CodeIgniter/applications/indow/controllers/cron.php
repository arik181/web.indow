<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Cron extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            exit;
        }

        $this->load->model('item_model');
        $this->load->database();
    }

    public function cutscript() {
        $this->queue_items_without_images();
        $items = $this->item_model->get_cut_queue();
        $total = count($items);

        $count = 0;
        foreach ($items as $item) {
            $count++;
            echo "Processing $count / $total\n";
            $this->item_model->update_cut_calculations($item);
        }
    }

    public function initial_items() {
        $count = $this->db->get('cut_calculations')->num_rows();
        if ($count) {
            exit("This script can only be run if there are no rows in the cut_calculations table.  It appears to have already run.\n\n");
        }
        $items = $this->db->where('deleted', 0)->get('orders_has_item')->result();
        $new_rows = array();
        foreach ($items as $item) {
            $new_rows[] = array('item_id' => $item->item_id, 'order_id' => $item->order_id);
        }
        $this->db->insert_batch('cut_calculations', $new_rows);
    }

    protected function queue_items_without_images() {
        $items = $this->db->query("
                SELECT orders_has_item.item_id, orders_has_item.order_id
                FROM orders_has_item
                LEFT JOIN cut_calculations ON orders_has_item.item_id=cut_calculations.item_id
                WHERE cut_calculations.id IS NULL
            ")->result();

        $new_rows = array();
        foreach ($items as $item) {
            $new_rows[] = array('item_id' => $item->item_id, 'order_id' => $item->order_id);
        }

        if (count($new_rows)) {
            $this->db->insert_batch('cut_calculations', $new_rows);
        }
    }
}