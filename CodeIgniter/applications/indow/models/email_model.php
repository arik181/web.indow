<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class email_model extends MM_Model {
    function __construct() {
        $this->test_mode = false; //turn this on to send all emails to the test address instead of the proper one.
        $this->test_address = 'aaronopela@gmail.com';
        $this->default_from_address = 'admin@modi.indowwindows.com';
        $this->default_from_name = 'Indow Windows';
    }

    protected function default_footer() {
        return "<br>Thanks,<br>Indow Team";
    }

    protected function format_freebird_email($customer, $user, $order_id, $username, $password) {
        ob_start();
        $login_link = 'http://' . $_SERVER['HTTP_HOST'] . '/orders/measure/' . $order_id;
        ?>
        Dear <?= @$user->first_name . ' ' . @$user->last_name ?>,<br><br>

        Thank you for initiating pre-order <?= $order_id ?> for <?= $customer['first_name'] . ' ' . $customer['last_name'] ?>. A laser measure kit will be sent to your customer within three business days. If you created this pre-order in error, contact Indow by close of business today to cancel or we cannot guarantee that we can halt shipment. You will be responsible for any associated cancellation fees once the Laser Measuring Kit has been shipped.<br><br>

        Below, you will find the login information to the Measure by Indow Portal. Please provide this information to your customer so they can enter their measure data.

        URL
        <a href="<?= $login_link ?>"><?= $login_link ?></a><br><br>

        <? if (!$password) { ?>
            Please use the username and password you created previously.<br>
        <? } else { ?>
            Username:<br><?= $username ?><br><br>
            Password:<br><?= $password ?><br>
        <? }?>
        <br><br><a href="http://go.indowwindows.com/MBI-usertemplate">Here</a> are a few templates you can use to send this information to your customer.
        <?= $this->default_footer() ?>
        <?
        return ob_get_clean();
    }

    public function send_freebird_measure_email($customer_id, $order_id) {
        if ($this->config->item('disable_email')) {
            return false;
        }
        $this->load->model('customer_model');
        $user = $this->db->where('orders.id', $order_id)->join('users', 'orders.created_by=users.id')->get('orders')->row();
        if (!$user) {
            return;
        }
        $customer = $this->customer_model->fetch_customer_info($customer_id);
        if (empty($customer['email_1'])) {
            return false;
        } else {
            $this->email->initialize(array('mailtype' => 'html'));
            $this->email->from($this->default_from_address, $this->default_from_name);
            if ($this->test_mode) {
                $to = $this->test_address;
            } else {
                $to = $user->email_1;
            }
            $this->email->to($to);
            $this->email->bcc('comfort@indowwindows.com');
            $this->email->subject('Indow Insert Measurement Form');

            list($username, $password) = $this->user_model->create_pass($customer_id);
            $message = $this->format_freebird_email($customer, $user, $order_id, $username, $password);
            $this->email->message($message);
            $this->email->send();
        }
    }

    protected function format_confirmation_email($customer, $order_id) {
        ob_start();
        $login_link = 'http://' . $_SERVER['HTTP_HOST'] . '/orders/confirmation/' . $order_id;
        ?>
        Dear <?= $customer['name'] ?>,<br><br>

        Please login to <a href="<?= $login_link ?>"><?= $login_link ?></a> and confirm your IndowWindows order.<br><br>

        <?= $this->default_footer() ?>
        <?
        return ob_get_clean();
    }

    public function send_order_confirmation_email($order_id) {
        if ($this->config->item('disable_email')) {
            return false;
        }
        $this->load->model('customer_model');
        $this->load->factory('OrderFactory');
        $order = $this->OrderFactory->get($order_id);

        list($cust_ids, $primary) = $this->customer_model->get_order_customers($order_id);
        $customer = $this->customer_model->fetch_customer_info($primary);

        $this->email->initialize(array('mailtype' => 'html'));
        $this->email->from($this->default_from_address, $this->default_from_name);
        if ($this->test_mode) {
            $this->email->to($this->test_address);
        } else {
            $this->email->to($customer['email_1']);
        }
        $this->email->subject('IndowWindows Order Confirmation');

        $message = $this->format_confirmation_email($customer, $order_id);
        $this->email->message($message);
        $this->email->send();
    }

    public function send_tech_email($estimate_id, $tech_id) {
        if ($this->config->item('disable_email')) {
            return false;
        }
        $this->load->model('quote_model');
        //$quote_id = $this->quote_model->create_from_estimate($estimate_id, $tech_id);
        $this->email->initialize(array('mailtype' => 'html'));
        $this->email->from($this->default_from_address, $this->default_from_name);
        $tech = $this->db->where('id', $tech_id)->get('users')->row();
        $tech_address = $tech->email_1;
        if ($this->test_mode) {
            $this->email->to($this->test_address);
        } else {
            $this->email->to($tech_address);
        }
        $customer = $this->estimate_model->get_primary_customer_name($estimate_id);
        $customer = $customer ? $customer : 'a job site.';
        ob_start();
        ?>
Hi,<br><br>

You have been assigned to measure for <?= $customer ?>.<br><br>

Here are the key steps:<br>
<ol>
    <li>Log into MAPP while you have an internet connection and verify the jobsite location is listed.</li>
    <li>MAPP is an offline application. DO NOT connect to the internet while measuring in MAPP.</li>
    <li>Specify the grade for each window. You can do this using MAPP or later in MODI when your are done measuring. Specialty grades are a great way to increase project size while bringing more value to your customers.</li>
    <li>While measuring, be on the lookout for things like:
    <ul>
        <li>Faded floors, pictures or drapes (suggest an upgrade to Museum Grade)</li>
        <li>Disruptive & repetitive sounds (Suggest an upgrade to Acoustic Grade)</li>
        <li>Bathroom windows that are exposed to neighbors (Suggest an upgrade to Privacy Grade)</li>
        <li>See the product data sheet for a complete list of specialty grade types</li>
    </ul></li>
    <li>
        Lastly, if this customer needs Indow inserts their neighbors may benefit too! Take doorknob hangers with you and put 20-30
        on the neighbor's doors. If you need more doorknob hangers the order form is
        <a href="http://www.indowwindows.com/wp-content/uploads/2015/09/Indow-Dealer-Mktg-091015.pdf">here</a>.
    </li>
</ol>
Thanks,<br>
Indow Team
        <?
        $email = ob_get_clean();
        $this->email->subject('You have been assigned to measure ' . $customer . '.');
        $this->email->message($email);
        $this->email->send();
    }

    public function send_password_reset_email($name, $email, $key) {
        $this->load->model('customer_model');
        $this->email->initialize(array('mailtype' => 'html'));
        $this->email->from($this->default_from_address, $this->default_from_name);
        if ($this->test_mode) {
            $to = $this->test_address;
        } else {
            $to = $email;
        }
        $this->email->to($to);
        $this->email->subject('Indow Password Reset');
        $link = 'http://' . $_SERVER['HTTP_HOST'] . '/change_password/' . $key;
        $message = 'Hello ' . $name . '<br><br> Please visit the following link to reset your MODI password:<br><br><a href="' . $link . '">' . $link . '</a><br><br>Thanks,<br>Indow';
        $this->email->message($message);
        $this->email->send();
    }
}
