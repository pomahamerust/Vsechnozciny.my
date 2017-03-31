<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Description : Controller of Welcome page
 * Respons URL : http://domain/welcome
 * 
 */
class Controller_Welcome extends Controller_Smarty_Ext {

    public function before() {
        parent::before();
    }

    public function action_widget($lang) {
        $page = ORM::factory('menu')->get_menu_by_controller('welcome');
        $this->template = "widget.tpl";
        $data = array(
            'page' => $page,
            'cart' => $_SESSION['cart_items'],
            'meta' => $page,
            "menu_index" => $page->index,
            "lang" => $this->lang,
            "content" => "widget.tpl",
            "lates" => orm::factory("product")->latest()
// "tops" => orm::factory("product")->get_top_products(),
//  "hits" => orm::factory("product")->hits(8),
// "actions" => orm::factory("product")->welcome_actions(8)
        );
        $this->view->assign($data);
    }

    public function action_index($lang) {
        //Helper_Mail::send_mail("yuko_vk@ukr.net", "yuko_vk@ukr.net", "ddd", "=?utf-8?B?" . base64_encode(__("order_subject") ) . "?=");

        $page = ORM::factory('menu')->get_menu_by_controller('welcome');
//   $this->session = Session::instance();
        /*  $c = 0;
          foreach (orm::factory("parameters_value")->find_all() as $one) {
          $c++;
          $one->order = $c;
          $one->save();
          } */

        $data = array(
            'page' => $page,
            'cart' => $_SESSION['cart_items'],
            'meta' => $page,
            'news1' => orm::factory("text")->get_latest_texts(2, 0),
            'news2' => orm::factory("text")->get_latest_texts(2, 3),
            "menu_index" => $page->index,
            "lang" => $this->lang,
            "content" => "welcome.tpl",
            "elections" => orm::factory("election")->all(),
            "lates" => orm::factory("product")->latest()
// "tops" => orm::factory("product")->get_top_products(),
//  "hits" => orm::factory("product")->hits(8),
// "actions" => orm::factory("product")->welcome_actions(8)
        );
        $this->view->assign($data);
    }

    public function action_unsubscribe($lang) {
        $page = ORM::factory('menu')
                ->where("controller", "=", "welcome")
                ->and_where("method", "=", "unsubscribe")
                ->find();
        $this->session = Session::instance();
        $email = $_GET["email"];

        $user = orm::factory("user")->where("email", "=", $email)->find();
        if ($user->newslatter && $user->newslatter == 1) {
            $user->newslatter = 0;
            $user->save();
        }
        foreach (orm::factory("settings_value")
                ->where("settings_name_id", "=", 26)
                ->and_where("value", "=", $email)
                ->find_all() as $one) {
            $one->delete();
        }
        $data = array(
            'page' => $page,
            'meta' => $page,
            "last_news" => orm::factory("text")->last_news(),
            "last_articles" => orm::factory("text")->last_articles(),
            "last_reviews" => orm::factory("text")->last_reviews(),
            "menu_index" => $page->index,
            "lang" => $this->lang,
            "content" => "unsubscribe.tpl",
        );
        $this->view->assign($data);
    }

    public function action_sync($lang) {
        $query = DB::select()->from('customers')
                ->join("orders1", "left")
                ->on("orders1.customers_id", "=", "customers.customers_id")
                ->group_by("customers.customers_email_address")
                ->execute();
        $auth = Auth::instance();
        $c = 0;

        foreach ($query as $one) {
            $user = orm::factory("user")->where("username", "=", $one["customers_email_address"])->find();
            if (!$user->id && $one["customers_email_address"]) {
                $c++;
//  $reg_ticket = Helper_Utils::generateUID();
// registations data is good to on.
                $user->username = $one["customers_email_address"];
                $user->email = $one["customers_email_address"];
                $user->phone = $one["customers_telephone"];
                $user->name = $one["customers_firstname"] . " " . $one["customers_lastname"];
                $user->surname = $one["customers_lastname"];
                $user->password = $auth->hash_password("12345");
                $user->save();
                $user->add('roles', ORM::factory('role', array('name' => 'login')));
                echo $c . "<br>";
            }
        }
    }

    public function action_sync1($lang) {
        $auth = Auth::instance();
        foreach (orm::factory("user")->find_all() as $user) {
//  if ($user->email == "yuko_vk@ukr.net") {
            echo $user->email . "<br/>";
            $password = helper_utils::generatePassword();
            $user->password = $auth->hash_password($password);
            $user->save();
            $data = array(
// "activation_link" => URL::base() . 'registration/activation/' . $reg_ticket,
                "username" => $user->name, //$login,
                "password" => $password,
            );
            $this->view->assign($data);
            $mail_body = $this->view->fetch("emails/reg_success_11.tpl");
            $mail_subject = "=?utf-8?B?" . base64_encode("Новый пароль") . "?=";
            Helper_Mail::send_mail($user->email, "info@astromagazin.net", $mail_body, $mail_subject);
// }
        }
        $this->auto_render = false;
    }

    public function action_trig() {
        foreach (orm::factory("manufacturer")->find_all() as $one) {
            $one->name_trans = Helper_Utils::str2url($one->name);
            $one->save();
        }
    }

    public function action_trig1() {
        $c = 110;
        foreach (orm::factory("product")->find_all() as $one) {
            $c++;
            $name = "$c
        <h3>Product Description</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dui ante, vulputate at pellentesque eget, viverra ac dolor. Morbi interdum tortor non leo aliquam ac aliquet nulla porta. Morbi id dolor massa, ut ornare augue. Morbi tincidunt magna bibendum enim tristique tristique. Quisque a massa tellus, ac tempor magna. Sed eget ligula tellus, nec pellentesque dolor. Phasellus dictum, mauris non mollis ornare, turpis libero faucibus orci, at fringilla urna leo sodales libero. Aliquam nec lectus mauris. Morbi lectus quam, convallis vel euismod a, auctor non dui. Etiam a nisi risus.</p>
        <p>Phasellus velit quam, ultrices et hendrerit vitae, suscipit nec dui. Sed at ligula vitae ligula pellentesque dictum. Duis lobortis auctor ipsum vel placerat. Phasellus nisi odio, ornare eget faucibus et, accumsan nec mauris. per conubia nostra, per inceptos himenaeos. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dui ante, vulputate at pellentesque eget, viverra ac dolor.Etiam a nisi risus.</p>
        <h1>Heading H1 30px</h1>
        <h2>Heading H2 26px</h2>
        <h3>Heading H3 20px</h3>
        <h4>Heading H4 18px</h4>
        <h5>Heading H5 15px</h5>
        <p>Phasellus velit quam, ultrices et hendrerit vitae, suscipit nec dui. Sed at ligula vitae ligula pellentesque dictum. Duis lobortis auctor ipsum vel placerat. Phasellus nisi odio, ornare eget faucibus et, accumsan nec mauris. per conubia nostra, per inceptos himenaeos.</p>

        ";
            $name2 = "$c
        <p><b>The wide product range of products for dogs , presented on our site, also includes and accessories for dogs , collars for dogs. At our site in this section, you can pick up at your leisure, with the style of your pet, dog collar and its accessories.</b></p>
        <ul>
          <li>inquisitive and active dogs tend to run away to explore the area or meet </li>
          <li>other dogs walking around in the yard, and there will be a very appropriate collars </li>
          <li>for dogs and accessories for dogs. Choose the right collar and necessary </li>
          <li>complement to it is not always easy, but all products on our site have a detailed description, </li>
          <li>including dog collars presented in sufficient detail. </li>
        </ul>
        ";
            $one->text = $name;
            $one->accessories = $name2;
            $one->save();
        }
    }

    public function action_trig2() {
        $c = 0;
        foreach (orm::factory("settings_value")->find_all() as $one) {
            $c++;
            $name = $one->value;
            $text = $one->text;
//   $text = $one->text;
//   $accessories = $one->accessories;
// $stext = $one->short_text;
            $one->en_value = "en $name";
            $one->ru_value = "ru $name";
            $one->de_value = "de $name";

            $one->en_text = "en $text";
            $one->ru_text = "ru $text";
            $one->de_text = "de $text";
//  $one->ru_text = "ru $text";
// $one->en_text = "en $text";
// $one->de_text = "de $text";
//   $one->ru_accessories = "ru $accessories";
//   $one->en_accessories = "en $accessories";
//   $one->de_accessories = "de $accessories";
            /*  $one->ru_short_text = "ru $stext";
              $one->en_short_text = "en $stext";
              $one->de_short_text = "de $stext"; */
            $one->save();
// if ($c == 2) {
//     $c = 0;
// }
        }
    }

    public function action_syncdb() {
        $this->auto_render = false;
        if (!$link = mysql_connect('localhost', 'root', '')) {
            echo 'Не удалось подключиться к mysql';
            exit;
        }

        if (!mysql_select_db('doggy', $link)) {
            echo 'Не удалось выбрать базу данных';
            exit;
        }

//  $sql = 'SELECT * FROM eshop_menus ';
// $sql = 'SELECT * FROM eshop_menus where language_id=1';
// $sql = 'SELECT * FROM eshop_menus_eshop_menus';
// $sql = 'SELECT * FROM parameters';
// $sql = 'SELECT * FROM parameters_values';
//  $sql = 'SELECT * FROM eshop_menus_parameters';
        $sql = 'SELECT * FROM products where language_id=1';
// $sql = 'SELECT * FROM eshop_menus_products';


        $result = mysql_query($sql, $link);

        if (!$result) {
            echo "Ошибка DB, запрос не удался\n";
            echo 'MySQL Error: ' . mysql_error();
            exit;
        }
        mysql_select_db('sobaki', $link);
        $price = 100;
        while ($row = mysql_fetch_assoc($result)) {
            $price++;
            $price++;
            $price++;
            $price++;
            /*  $test = orm::factory("eshop_menu")
              ->where("id", "=", $row["eshop_menu_id"])
              ->find();
              if ($test->id) {
              $arr = orm::factory("eshop_menus_parameter");
              $arr->id = $row["id"];
              $arr->eshop_menu_id = $row["eshop_menu_id"];
              $arr->parameter_id = $row["parameter_id"];
              $arr->order = $row["order"];
              $arr->save();
              } */
            /* $test = orm::factory("product")
              ->where("id", "=", $row["product_id"])
              ->find();
              if ($test->id) {
              $arr = orm::factory("eshop_menus_product");
              $arr->id = $row["id"];
              $arr->eshop_menu_id = $row["eshop_menu_id"];
              $arr->product_id = $row["product_id"];
              $arr->order = $row["order"];
              $arr->save();
              } */
            $arr = orm::factory("product")
                    ->where("id", "=", $row["id"])
                    ->find();
            if ($arr->id) {
                $arr->img = $row["img"];
                $arr->save();
            }
            /*
              $arr->id = $row["id"];
              $arr->index = $row["index"];
              $arr->language_id = 1; // $row["id"];
              //  $arr->eshop_menu_id = $row["index"];
              $arr->order = $row["order"];
              $arr->watch = $row["watch"];
              $arr->price = $price;
              $arr->name_trans = $row["name_trans"];
              $arr->name = $row["name"];
              $arr->en_name = $row["name"];
              $arr->ru_name = $row["name"];
              $arr->de_name = $row["name"];
              $arr->img = $row["img"];
              $arr->breadcrumb = $row["breadcrumb"];
              $arr->articul = $row["articul"];
              //  $arr->id = $row["id"];
              $arr->save();
              /* $arr = orm::factory("parameter");
              $arr->id = $row["id"];
              $arr->index = $row["index"];
              $arr->language_id = 1;
              $arr->en_name = $row["name"];
              $arr->de_name = $row["name"];
              $arr->ru_name = $row["name"];
              $arr->admin_title = $row["name"];
              $arr->name_trans = helper_utils::str2url(trim($row["name"]));

              $arr->save(); */
            /* $arr = orm::factory("parameters_value");
              $arr->id = $row["id"];
              $arr->parameter_id = $row["parameter_id"];
              $arr->value = $row["value"];
              $arr->en_value = $row["value"];
              $arr->ru_value = $row["value"];
              $arr->de_value = $row["value"];
              $arr->name_trans = helper_utils::str2url(trim($row["value"]));

              $arr->save(); */
            /* $arr = orm::factory("eshop_menu");
              $arr->id = $row["id"];
              $arr->language_id = 1;
              $arr->index = ORM::factory("eshop_menu")
              ->order_by('index', 'desc')
              ->limit(1)->find()->index + 1;
              $arr->order = ORM::factory("eshop_menu")
              ->order_by('order', 'desc')
              ->limit(1)->find()->index + 1;
              $arr->watch = $row["watch"];
              $arr->is_group = $row["is_group"];
              $arr->breadcrumb = $row["breadcrumb"];
              $arr->name = $row["name"];
              $arr->name_trans = $row["name_trans"];
              $arr->en_name = $row["name"];
              $arr->de_name = $row["name"];
              $arr->ru_name = $row["name"];

              $arr->save(); */
            /* echo $row["eshop_menu_id"]."<br/>";
              $test = orm::factory("eshop_menu")
              ->where("id", "=", $row["eshop_menu_id"])
              ->find();
              if ($test->id) {
              $arr = orm::factory("eshop_menus_eshop_menu");
              $arr->eshop_menu_id = $row["eshop_menu_id"];
              $arr->next_menu_id = $row["next_menu_id"];
              $arr->order = $row["order"];
              $arr->save();
              } */
            echo "1";
        }
    }

    /*
     * 
     */

    function encodestr($str) {
        $str = mb_convert_encoding($str, 'UTF-32', 'UTF-8'); //big endian
        $split = str_split($str, 4);

        $res = "";
        foreach ($split as $c) {
            $cur = 0;
            for ($i = 0; $i < 4; $i++) {
                $cur |= ord($c[$i]) << (8 * (3 - $i));
            }
            $res .= "&#" . $cur . ";";
        }
        return $res;
    }

    public function action_syncxls() {
        $this->auto_render = false;
        $filename = 'sync/sync.xls';
        $ex = (file_exists($filename)) ? true : false;
        if ($ex) {
            require_once 'excelreader.php';
            $data = new Spreadsheet_Excel_Reader("sync/sync.xls", false);
            $menu_id = 436;
            for ($row = 2; $row <= $data->rowcount($sheet_index = 0); $row++) {
//  echo mb_detect_encoding(trim($data->val($row, 'B', $sheet_index)), array('UTF-8', 'Windows-1251', 'KOI8-R', 'ISO-8859-15', 'ISO-8859-1', 'ISO-8859-5')) . "<br/>";
// echo iconv("ISO-8859-15", "utf-8", trim($data->val($row, 'B', $sheet_index)));
//  echo trim($data->val($row, 'B', $sheet_index));
                $p = orm::factory("product")
                        ->where("imgsrc", "=", "http://www.luxuryrepublic.cz" . trim($data->val($row, 'M', $sheet_index)))
                        ->find();
                if ($p->id) {
                    echo trim($data->val($row, 'I', $sheet_index));
//  if (mb_detect_encoding(trim($data->val($row, 'B', $sheet_index)), array('UTF-8', 'Windows-1251', 'KOI8-R', 'ISO-8859-15', 'ISO-8859-1', 'ISO-8859-5')) == "ISO-8859-15")
//     $name = iconv("ISO-8859-15", "utf-8", trim($data->val($row, 'B', $sheet_index)));
//   else
//      $name = trim($data->val($row, 'B', $sheet_index));
//   $p->name = $name; // mb_convert_encoding(trim($data->val($row, 'B', $sheet_index)), "utf-8", "auto");
//   $p->cz_name = $name; // mb_convert_encoding(trim($data->val($row, 'B', $sheet_index)), "utf-8", "ISO-8859-5,JIS, eucjp-win, sjis-win");
//  if (trim($data->val($row, 'I', $sheet_index)) != 0)
//     $p->price_old = 0;//$price + round($price * (1 - (int) ($data->val($row, 'I', $sheet_index)) / 100));
//$p->save();
//  echo 'exists<br>';
                } else {
                    if (mb_detect_encoding(trim($data->val($row, 'B', $sheet_index)), array('UTF-8', 'Windows-1251', 'KOI8-R', 'ISO-8859-15', 'ISO-8859-1', 'ISO-8859-5')) == "ISO-8859-15")
                        $name = iconv("ISO-8859-15", "utf-8", trim($data->val($row, 'B', $sheet_index)));
                    else
                        $name = trim($data->val($row, 'B', $sheet_index));

                    $p->order = ORM::factory("product")
                                    ->order_by('order', 'desc')
                                    ->limit(1)->find()->order + 1;
                    $p->name = $name;
                    $p->cz_name = $name;
                    $p->collection = trim($data->val($row, 'L', $sheet_index));
                    $p->name_trans = helper_utils::str2url($name);
                    $p->articul = trim($data->val($row, 'A', $sheet_index));
                    $p->imgsrc = "http://www.luxuryrepublic.cz" . trim($data->val($row, 'M', $sheet_index));
                    $p->text = trim($data->val($row, 'C', $sheet_index));
                    $p->cz_text = trim($data->val($row, 'D', $sheet_index));
                    $p->cz_short_text = trim($data->val($row, 'C', $sheet_index));

                    $p->quantity = trim($data->val($row, 'J', $sheet_index));
                    $p->index = ORM::factory("product")
                                    ->order_by('index', 'desc')
                                    ->limit(1)->find()->index + 1;
                    $p->price = trim($data->val($row, 'E', $sheet_index));
                    $price = $data->val($row, 'E', $sheet_index);
                    if (trim($data->val($row, 'I', $sheet_index)) != 0)
                        $p->price_old = $price + round($price * (1 - (int) ($data->val($row, 'I', $sheet_index)) / 100));

                    $p->language_id = 1;
                    $p->watch = 1;
//   $name = strtolower(trim($data->val($row, 'D', $sheet_index)));
//   $name = str_replace(" ", "_", $name);
//    $name_main = $name . ".jpg";
//        $p->img = $name_main;
                    $p->save();
// $name_b = $name . "_back.jpg";
//1

                    /*  $file = "uploads/products/img/" . $name_main;
                      if (file_exists($file)) {
                      //Do crop if it's defined
                      $image = Image::factory($file);
                      $image->resize(346, null);
                      $result = $image->save("uploads/products/prw/" . $name_main);
                      } */
//eshop
                    $rel = orm::factory("eshop_menus_product");
                    $rel->eshop_menu_id = $menu_id;
                    $rel->product_id = $p->id;
                    $rel->order = ORM::factory('eshop_menus_product')
                                    ->order_by('order', 'desc')
                                    ->limit(1)->find()->order + 1;
                    $rel->save();
                    $engine = new Helper_Catalog_Scanner($p);
//pvl_rel
                    /* $pval = strtoupper(trim($data->val($row, 'C', $sheet_index)));
                      $pv = orm::factory("parameters_value")
                      ->where("value", "=", $pval)
                      ->and_where("parameter_id", "=", 25)
                      ->find();
                      if (!$pv->id) {
                      $pv->parameter_id = 25;
                      $pv->value = $pval;
                      $pv->cz_value = $pval;
                      $pv->name_trans = helper_utils::str2url($pval);
                      $pv->save();
                      }
                      $pr_pv = orm::factory("products_parameter")
                      ->where("product_id", "=", $p->id)
                      ->and_where("parameters_value_id", "=", $pv->id)
                      ->find();
                      $pr_pv->product_id = $p->id;
                      $pr_pv->parameter_id = 25;
                      $pr_pv->parameters_value_id = $pv->id;
                      $pr_pv->value = trim($data->val($row, 'F', $sheet_index));
                      $pr_pv->text = trim($data->val($row, 'B', $sheet_index));
                      $pr_pv->save(); */
                }
                echo "1 <br/>";
            }
        }
    }

    public function action_syncxls2() {
        foreach (orm::factory("product")->find_all() as $one) {
            $engine = new Helper_Catalog_Scanner($one); //generate breadcrumbs
        }
        /* foreach (orm::factory("parameters_value")->find_all() as $one) {
          $one->name_trans = helper_utils::str2url($one->value);
          $one->save();
          } */
    }

    public function action_syncxls3() {
        foreach (orm::factory("product")->find_all() as $one) {
            $name = strtolower($one->name);
            $ph1 = str_replace(" ", "-", $name) . "-1-3d.jpg";
            $photo = orm::factory("products_photo")
                    ->where("img", "=", $ph1)
                    ->find();
            if (!$photo->id) {
                $photo->watch = 1;
                $photo->product_id = $one->id;
                $photo->img = $ph1;
                $photo->save();
                $file = "uploads/products/img/" . $ph1;
                if (file_exists($file)) {
//Do crop if it's defined
                    $image = Image::factory($file);
                    $image->resize(346, null);
                    $result = $image->save("uploads/products/prw/" . $ph1);
                }
            }
        }
    }

    public function action_syncxls4() {
        foreach (orm::factory("products_photo")->find_all() as $one) {

            if ($one->img) {

                $file = "uploads/products/img/" . $one->img;
                if (file_exists($file)) {
//Do crop if it's defined
                    $image = Image::factory($file);
                    $image->resize(800, null);
                    $result = $image->save("uploads/products/img/" . $one->img);
                }
            }
        }
    }

    public function action_syncxls1() {
        $this->auto_render = false;
        $filename = 'sync/sync1.xls';
        $ex = (file_exists($filename)) ? true : false;
        if ($ex) {
            require_once 'excelreader.php';
            $data = new Spreadsheet_Excel_Reader("sync/sync1.xls", false);
            for ($row = 4; $row <= $data->rowcount($sheet_index = 0); $row++) {
                $p = orm::factory("product")
                        ->where("name", "=", trim($data->val($row, 'B', $sheet_index)))
                        ->find();
                if ($p->id) {
                    $price = str_replace("?", "", trim($data->val($row, 'E', $sheet_index)));
                    /*   $p->price = str_replace(",", ".", trim($price));
                      $p->save(); */
                    echo $price;
                }
            }
        }
    }

    public function action_eng() {
        $this->auto_render = false;
        $c = 0;

        $name = "cz_name";
        $cid = 1;
        $st = "Svatební šperky v této soupravě mají ozdoby ze štrasových řetízků.";
        foreach (orm::factory("product")->find_all() as $one) {
            $one->cz_short_text = $st . " $cid";
            $one->save();
            /* $rel = orm::factory("products_color");
              $rel->product_id = $one->id;
              $rel->color_id = $cid;
              $rel->save();
              if ($cid == 6)
              $cid = 0;
              $cid++;
              $rel = orm::factory("products_color");
              $rel->product_id = $one->id;
              $rel->color_id = $cid;
              $rel->save();
              if ($cid == 6)
              $cid = 0;
              $cid++;
              $rel = orm::factory("products_color");
              $rel->product_id = $one->id;
              $rel->color_id = $cid;
              $rel->save();
              if ($cid == 6)
              $cid = 0;
              $cid++;
              $rel = orm::factory("products_color");
              $rel->product_id = $one->id;
              $rel->color_id = $cid;
              $rel->save();
              if ($cid == 6)
              $cid = 0;
              $cid++;
              //$price = $price + 100;
              // $one->price = $price;
              // $one->save();
              //  if (!$one->cz_text) {
              //      $one->cz_text = $one->text;
              //      $one->save();
              //   }
              // $engine = new Helper_Catalog_Scanner($one); //generate breadcrumbs
              //  $rel = orm::factory("eshop_menus_product");
              //  $rel->product_id = $one->id;
              //  $rel->eshop_menu_id = 439;
              //  $rel->save(); */
            $cid++;
        }
    }

    public function action_xml1rrr() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.zlatnictvitrimama.cz/Katalog-Export-ZboziCz/Vychozi/t845.xml");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        $c = 0;
        $k = 2400;
        $l = 0;
        foreach ($xml as $one) {
            $c++;
            $k++;
            $articul = trim($one->ITEM_ID);
            if ($c <= 3500 && $c > 4500) {
                $ex = explode('Prsteny', trim($one->CATEGORYTEXT));

                if (count($ex) > 1) {
                    echo $one->CATEGORYTEXT . "<br>";
                    $prd = orm::factory("product")
                            ->where("name", "=", $one->PRODUCT)
                            ->find();
                    if (!$prd->id) {
                        $n = explode(" ", $one->PRODUCT);
                        $articul = $n[count($n) - 1];
                        $prd->language_id = 1;
                        $prd->index = $k;
                        $prd->articul = $articul;
                        $prd->name = $one->PRODUCT;
                        $prd->cz_name = $one->PRODUCT;

                        $prd->name_trans = helper_utils::str2url($one->PRODUCT);
                        $prd->imgsrc = $one->IMGURL;
                        // $prd->img = $img;
                        $prd->price = str_replace(",", ".", $one->PRICE_VAT);
                        $prd->text = trim($one->DESCRIPTION);
                        $prd->short_text = trim($one->DESCRIPTION);
                        $prd->watch = 1;
                        $prd->save();
                    } else {
                        //$prd->name_trans = helper_utils::str2url($one->PRODUCT);
                        //$prd->cz_name = $one->PRODUCT;
                        //$prd->save();
                        $rel = orm::factory("eshop_menus_product")
                                ->where("product_id", "=", $prd->id)
                                ->and_where("eshop_menu_id", "=", 494)
                                ->find();
                        if (!$rel->id) {
                            $rel->product_id = $prd->id;
                            $rel->eshop_menu_id = 494;
                            $rel->save();
                            $engine = new Helper_Catalog_Scanner($prd);
                        }
                    }
                }
            }
            /* $prd = orm::factory("product")
              ->where("articul", "=", $articul)
              ->find();
              if (!$prd->id) {
              $prd->language_id = 1;
              $prd->index = $c;
              $prd->articul = $articul;
              $prd->name = $one->PRODUCTNAME;
              $img = explode("?", $one->IMGURL);
              $img = explode("/", $img[0]);
              $img = $img[count($img) - 1];
              $prd->img = $img;
              $prd->price = str_replace(",", ".", $one->PRICE_VAT);
              $prd->text = trim($one->DESCRIPTION);
              $prd->watch = 1;
              $prd->save();
              echo "p_" . $c . "<br>";
              } else {
              $cat = explode("|", trim($one->CATEGORYTEXT));
              $em = orm::factory("eshop_menu")
              ->where("name", "=", trim($cat[count($cat) - 1]))
              ->find();
              if ($em->id) {
              $rel = orm::factory("eshop_menus_product")
              ->where("product_id", "=", $prd->id)
              ->and_where("eshop_menu_id", "=", $em->id)
              ->find();
              if (!$rel->id) {
              $rel->product_id = $prd->id;
              $rel->eshop_menu_id = $em->id;
              $rel->save();
              }
              echo "ok<br/>";
              } else {
              echo "error<br/>";
              }
              } */
        }
        echo $l;
    }

    public function action_fixer() {
        $this->auto_render = false;
        $i = 0;
        /* foreach (orm::factory("eshop_menu")->find_all() as $one) {
          $i++;
          $one->index = $i;
          $one->cz_name =$one->name;
          //  $one->cz_text =$one->name;
          $one->name_trans = Helper_Utils::str2url($one->name);

          $one->save();
          } */
        foreach (orm::factory("product")->find_all() as $one) {
            $one->cz_text = $one->text;
            $one->save();
            /*  $file = "uploads/products/img/" . $one->img;
              if (file_exists($file) && !file_exists("uploads/products/prw/" . $one->img)) {
              //Do crop if it's defined
              $image = Image::factory($file);
              $image->resize(265, null);
              $result = $image->save("uploads/products/prw/" . $one->img);
              }
              /* $i++;
              $one->index = $i;
              $one->cz_name = $one->name;
              $one->cz_text = $one->text;
              $one->name_trans = Helper_Utils::str2url($one->name);
              $one->save();
              $engine = new Helper_Catalog_Scanner($one); //generate breadcrumbs
              echo "$i <br/>"; */
            echo "1";
        }
        /*        $this->auto_render = false;

          $xmlString = file_get_contents("http://www.bizuterie-a-doplnky.cz/heureka/export/products.xml");
          // echo $xmlString;
          $xml = new SimpleXMLElement($xmlString);
          $c = 0;
          $k = 1;
          foreach ($xml as $one) {
          $c++;
          $img = explode("?", $one->IMGURL);
          $img = explode("/", $img[0]);
          $img = $img[count($img) - 1];
          $filename = "uploads/products/img/$img";
          $your_path = "uploads/products/img/$img";
          if (!file_exists($filename)) {
          echo $c."<br/>";
          copy($one->IMGURL, $your_path);
          } else {
          //echo "Файл $filename не существует";
          }
          // copy($one->IMGURL, $your_path);
          echo "ok<br/>";
          } */
    }

    public function action_xml11($lang) {
        $this->auto_render = false;
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $shop = $xml->createElement('SHOP');
        $shop->setAttribute("xmlns", "http://www.zbozi.cz/ns/offer/1.0");
        $xml->appendChild($shop);
        $products = DB::select(DB::expr("products.*,eshop_menus.cz_name as esname"))
                ->from("products")
                ->join("eshop_menus_products", "left")
                ->on("eshop_menus_products.product_id", "=", "products.id")
                ->join("eshop_menus", "left")
                ->on("eshop_menus_products.eshop_menu_id", "=", "eshop_menus.id")
                ->where('products.watch', '=', 1)
                ->and_where('eshop_menus.watch', '=', 1)
                ->and_where("products.breadcrumb", "!=", null)
                ->and_where('products.language_id', '=', 1)
                ->and_where('products.price', '>', 0)
                ->group_by("products.cz_name")
                ->execute();

        foreach ($products as $key => $one) {
            $go = FALSE;
            if ($one["imgsrc"]) {
                $t = explode("http", $one["imgsrc"]);
                if (count($t) < 2) {
                    
                }
                $go = true;
            } else {
                if ($one["img"]) {
                    $go = true;
                }
            }
            if ($go) {
                $shopitem = $xml->createElement('SHOPITEM');
                $shop->appendChild($shopitem);
                $ITEM_ID = $xml->createElement('ITEM_ID', $one["id"]);
                $shopitem->appendChild($ITEM_ID);
                $PRODUCTNAME = $xml->createElement('PRODUCTNAME', $one["cz_name"] . " " . $one["articul"]);
                $shopitem->appendChild($PRODUCTNAME);
                $DESCRIPTION = $xml->createElement('DESCRIPTION', $one["cz_short_text"]);
                $shopitem->appendChild($DESCRIPTION);
                $CATEGORYTEXT = $xml->createElement('CATEGORYTEXT', $one["esname"]);
                $shopitem->appendChild($CATEGORYTEXT);
                $ean = $xml->createElement('EAN', self::generateEAN($one["id"]));
                $shopitem->appendChild($ean);
                $PRODUCTNO = $xml->createElement('PRODUCTNO', "MC" . $one["id"] . "CZ");
                $shopitem->appendChild($PRODUCTNO);
                $EXTRA_MESSAGE = $xml->createElement('EXTRA_MESSAGE', "free_delivery");
                $shopitem->appendChild($EXTRA_MESSAGE);
                $URL = $xml->createElement('URL', url::base() . "katalog/" . $one["name_trans"] . "-" . $one["id"] . ".html");
                $shopitem->appendChild($URL);
                $DELIVERY_DATE = $xml->createElement('DELIVERY_DATE', '25');
                $shopitem->appendChild($DELIVERY_DATE);
                if ($one["imgsrc"]) {
                    $IMGURL = $xml->createElement('IMGURL', $one["imgsrc"]);
                    $shopitem->appendChild($IMGURL);
                } else {
                    $IMGURL = $xml->createElement('IMGURL', url::base() . "uploads/products/img/" . $one["img"]);
                    $shopitem->appendChild($IMGURL);
                }
                $PRICE_VAT = $xml->createElement('PRICE_VAT', $one["price"]);
                $shopitem->appendChild($PRICE_VAT);
            }
        }
        $xml->save("data.xml");
        Request::instance()->redirect('/feeddata.xml');
    }

    public function action_xml3($lang) {
        $this->auto_render = false;
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $shop1 = $xml->createElement('rss');
        $shop1->setAttribute("version", "2.0");
        $shop1->setAttribute("xmlns:g", "http://base.google.com/ns/1.0");
        $shop1->setAttribute("xmlns:c", "http://base.google.com/cns/1.0");
        $xml->appendChild($shop1);
        $products = orm::factory("product")->all();
        $shop = $xml->createElement('channel');
        $shop1->appendChild($shop);
        $t = $xml->createElement('title', 'Pošta Zdarma');
        $shop->appendChild($t);
        $t = $xml->createElement('link', url::base());
        $shop->appendChild($t);
        foreach ($products as $key => $one) {
            $shopitem = $xml->createElement('item');
            $shop->appendChild($shopitem);
            $PRODUCTNAME = $xml->createElement('title', $one->name . " " . $one->articul);
            $shopitem->appendChild($PRODUCTNAME);
            $URL = $xml->createElement('link', url::base() . "katalog/" . $one->name_trans . "-" . $one->id . ".html");
            $shopitem->appendChild($URL);
            $ITEM_ID = $xml->createElement('g:id', $one->id);
            $shopitem->appendChild($ITEM_ID);

            /* $PRODUCT = $xml->createElement('PRODUCT', $one->name);
              $shopitem->appendChild($PRODUCT); */
            $DESCRIPTION = $xml->createElement('description', $one->short_text);
            $shopitem->appendChild($DESCRIPTION);

            $bc = $one->get_objected_way($one->breadcrumb);

            $cs = "";
            $c = 0;
            foreach ($bc as $val) {
                echo $val->cz_name;
                $c++;
                $cs.=$val->cz_name;
                if ($c != count($bc))
                    $cs.=" &amp; ";
            }
            if ($one->google_product_category) {
                $CATEGORYTEXT = $xml->createElement('g:google_product_category', $one->google_product_category);
                $shopitem->appendChild($CATEGORYTEXT);
            }
            /*  $CATEGORYTEXT = $xml->createElement('g:google_product_categor', $cs);
              $shopitem->appendChild($CATEGORYTEXT); */
            $condition = $xml->createElement('g:condition', "new");
            $shopitem->appendChild($condition);
            $CATEGORYTEXT1 = $xml->createElement('g:product_type', $cs);
            $shopitem->appendChild($CATEGORYTEXT1);

            $shipping = $xml->createElement('g:shipping');
            $shopitem->appendChild($shipping);

            $country = $xml->createElement('g:country', 'CZ');
            $shipping->appendChild($country);

            $service = $xml->createElement('g:service', 'Standard');
            $shipping->appendChild($service);

            $price = $xml->createElement('g:price', '0 CZK');
            $shipping->appendChild($price);


            /*  $DELIVERY_DATE = $xml->createElement('DELIVERY_DATE', '1');
              $shopitem->appendChild($DELIVERY_DATE); */



            $photo = $one->get_main_photo();
            if ($photo->img) {
                $IMGURL = $xml->createElement('g:image_link', url::base() . "uploads/products/img/" . $photo->img);
                $shopitem->appendChild($IMGURL);
            } elseif ($one->imgsrc) {
                $IMGURL = $xml->createElement('g:image_link', $one->imgsrc);
                $shopitem->appendChild($IMGURL);
            }

            $DELIVERY_DATE = $xml->createElement('g:availability', 'preorder');
            $shopitem->appendChild($DELIVERY_DATE);

            $mpn = $xml->createElement('g:mpn', $one->articul);
            $shopitem->appendChild($mpn);

            $gtin = $xml->createElement('g:gtin', self::generateEAN($one->id));
            $shopitem->appendChild($gtin);

            $brand = $xml->createElement('g:brand', "vsechnozciny");
            $shopitem->appendChild($brand);



            $PRICE_VAT = $xml->createElement('g:price', $one->price . " CZK");
            $shopitem->appendChild($PRICE_VAT);


            /*  $PRODUCTNO = $xml->createElement('PRODUCTNO', $one->id);
              $shopitem->appendChild($PRODUCTNO); */

            //----------------PARAMS
            /* foreach ($one->get_parametersg() as $val) {
              $PARAM = $xml->createElement('PARAM');
              $shopitem->appendChild($PARAM);

              $PARAM_NAME = $xml->createElement('PARAM_NAME', $val->parameter->name);
              $PARAM->appendChild($PARAM_NAME);
              foreach ($one->get_values_to_param($val->parameter) as $valuep) {
              $VAL = $xml->createElement('VAL', $valuep->parameters_value->value);
              $PARAM->appendChild($VAL);
              }
              } */

            //----------------DELIVERY
            /*  foreach (ORM::factory('payment_method')->get_methods() as $val) {
              $DELIVERY = $xml->createElement('DELIVERY');
              $shopitem->appendChild($DELIVERY);
              $DELIVERY_ID = $xml->createElement('DELIVERY_ID', strtoupper(helper_synctranslit::ToLat(helper_synctranslit::ToLat($val->name))));
              $DELIVERY->appendChild($DELIVERY_ID);

              $DELIVERY_PRICE = $xml->createElement('DELIVERY_PRICE', $val->price);
              $DELIVERY->appendChild($DELIVERY_PRICE);
              } */
        }
        //$product->setAttribute("id", "123");
        //$product->appendChild(new DomAttr('id', '123'));
        $xml->save("feeddata3.xml");
        Request::instance()->redirect('/feeddata3.xml');
    }

    public function action_xml2($lang = "cz") {
        $name = $lang . "_name";

        $this->auto_render = false;
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $shop = $xml->createElement('SERVER');
        $xml->appendChild($shop);
        $products = orm::factory("product")->all();
        foreach ($products as $key => $one) {
            $shopitem = $xml->createElement('DEAL');
            $shop->appendChild($shopitem);
            $ITEM_ID = $xml->createElement('ID', $one->id);
            $shopitem->appendChild($ITEM_ID);
            $CITY = $xml->createElement('CITY', 'celá ČR');
            $shopitem->appendChild($CITY);

            $PRODUCTNAME = $xml->createElement('TITLE', $one->$name);
            $shopitem->appendChild($PRODUCTNAME);

            $URL = $xml->createElement('URL', helper_urls::product($one, true));
            $shopitem->appendChild($URL);
            $photo = $one->get_main_photo();
            if ($photo->img) {
                $IMGURL = $xml->createElement('IMAGE', url::base() . "uploads/products/img2/" . $photo->img);
                $shopitem->appendChild($IMGURL);
            }

            if ($one->short_text)
                $DESCRIPTION = $xml->createElement('DESCRIPTION_FULL', $one->short_text);
            else
                $DESCRIPTION = $xml->createElement('DESCRIPTION_FULL', htmlspecialchars(strip_tags($one->cz_text)));
            $shopitem->appendChild($DESCRIPTION);

            /* $EAN = $xml->createElement('EAN', $one->articul);
              $shopitem->appendChild($EAN);

              $DELIVERY_DATE = $xml->createElement('DELIVERY_DATE', $one->delivery_date);
              $shopitem->appendChild($DELIVERY_DATE); */




            if ($one->discount) {
                $x1 = (100 * $one->price) / (100 - $one->discount);

                $PRICE_VAT = $xml->createElement('FINAL_PRICE', $one->price);
                $shopitem->appendChild($PRICE_VAT);
                $PRICE_VAT = $xml->createElement('ORIGINAL_PRICE', $x1);
                $shopitem->appendChild($PRICE_VAT);
                //  100%=100
                // 40%=x
                $x = (100 * ($one->price_old - $one->price)) / $one->price;
                $DISCOUNT = $xml->createElement('DISCOUNT', $one->discount);
                $shopitem->appendChild($DISCOUNT);
            } else {
                $PRICE_VAT = $xml->createElement('FINAL_PRICE', $one->price);
                $shopitem->appendChild($PRICE_VAT);
                $PRICE_VAT = $xml->createElement('ORIGINAL_PRICE', $one->price);
                $shopitem->appendChild($PRICE_VAT);
                $DISCOUNT = $xml->createElement('DISCOUNT', 0);
                $shopitem->appendChild($DISCOUNT);
            }



            $dt = date("Y-m-d h:i:s");
            $DEAL_START = $xml->createElement('DEAL_START', $dt);
            $shopitem->appendChild($DEAL_START);

            $dt2 = strtotime($dt);
            $dt2 = strtotime("+3 day", $dt2);
            $dt2 = date("Y-m-d h:i:s", $dt2);
            $DEAL_END = $xml->createElement('DEAL_END', $dt2);
            $shopitem->appendChild($DEAL_END);

            $CUSTOMERS = $xml->createElement('CUSTOMERS', $one->orders);
            $shopitem->appendChild($CUSTOMERS);

            $MIN_CUSTOMERS = $xml->createElement('MIN_CUSTOMERS', '0');
            $shopitem->appendChild($MIN_CUSTOMERS);

            $MAX_CUSTOMERS = $xml->createElement('MAX_CUSTOMERS', '0');
            $shopitem->appendChild($MAX_CUSTOMERS);

            $bc = $one->get_objected_way($one->breadcrumb);
            $cs = "";
            $c = 0;
            foreach ($bc as $val) {
                $c++;
                $cs.=$val->name;
                if ($c != count($bc))
                    $cs.=" | ";
            }

            $CATEGORYTEXT = $xml->createElement('TAG', $cs);
            $shopitem->appendChild($CATEGORYTEXT);
            $cid = $one->eshop_menus->find()->id;


            $CATEGORYTEXT = $xml->createElement('CATEGORY_ID', $cid);
            $shopitem->appendChild($CATEGORYTEXT);

            /* $PRODUCTNO = $xml->createElement('PRODUCTNO', $one->id);
              $shopitem->appendChild($PRODUCTNO); */

            //----------------PARAMS
            /*  foreach ($one->get_parametersg() as $val) {
              $PARAM = $xml->createElement('PARAM');
              $shopitem->appendChild($PARAM);

              $PARAM_NAME = $xml->createElement('PARAM_NAME', $val->parameter->name);
              $PARAM->appendChild($PARAM_NAME);
              foreach ($one->get_values_to_param($val->parameter) as $valuep) {
              $VAL = $xml->createElement('VAL', $valuep->parameters_value->value);
              $PARAM->appendChild($VAL);
              }
              } */

            //----------------DELIVERY
            /*  foreach (ORM::factory('payment_method')->get_methods() as $val) {
              $DELIVERY = $xml->createElement('DELIVERY');
              $shopitem->appendChild($DELIVERY);
              $DELIVERY_ID = $xml->createElement('DELIVERY_ID', strtoupper(helper_synctranslit::ToLat(helper_synctranslit::ToLat($val->name))));
              $DELIVERY->appendChild($DELIVERY_ID);

              $DELIVERY_PRICE = $xml->createElement('DELIVERY_PRICE', $val->price);
              $DELIVERY->appendChild($DELIVERY_PRICE);
              } */
        }
        //$product->setAttribute("id", "123");
        //$product->appendChild(new DomAttr('id', '123'));
        $xml->save("feeddata2.xml");
        Request::instance()->redirect('/feeddata2.xml');
    }

    public function action_xml1($lang = "cz") {
        $name = $lang . "_name";

        $this->auto_render = false;
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $shop = $xml->createElement('shop');
        $xml->appendChild($shop);
        $products = orm::factory("product")->all();
        foreach ($products as $key => $one) {
            $shopitem = $xml->createElement('shopitem');
            $shop->appendChild($shopitem);
            $ITEM_ID = $xml->createElement('ITEM_ID', $one->id);
            $shopitem->appendChild($ITEM_ID);
            $PRODUCTNAME = $xml->createElement('PRODUCTNAME', $one->$name);
            $shopitem->appendChild($PRODUCTNAME);
            $PRODUCT = $xml->createElement('PRODUCT', $one->$name);
            $shopitem->appendChild($PRODUCT);
            if ($one->short_text)
                $DESCRIPTION = $xml->createElement('DESCRIPTION', $one->short_text);
            else
                $DESCRIPTION = $xml->createElement('DESCRIPTION', htmlspecialchars(strip_tags($one->text)));
            $shopitem->appendChild($DESCRIPTION);
            $EAN = $xml->createElement('EAN', $one->articul);
            $shopitem->appendChild($EAN);

            $DELIVERY_DATE = $xml->createElement('DELIVERY_DATE', $one->delivery_date);
            $shopitem->appendChild($DELIVERY_DATE);

            $URL = $xml->createElement('URL', helper_urls::product($one, true));
            $shopitem->appendChild($URL);
            $photo = $one->get_main_photo();
            if ($photo->img) {
                $IMGURL = $xml->createElement('IMGURL', url::base() . "uploads/products/img/" . $photo->img);
                $shopitem->appendChild($IMGURL);
            }
            $PRICE_VAT = $xml->createElement('PRICE_VAT', $one->price);
            $shopitem->appendChild($PRICE_VAT);
            $bc = $one->get_objected_way($one->breadcrumb);
            $cs = "";
            $c = 0;
            foreach ($bc as $val) {
                $c++;
                $cs.=$val->name;
                if ($c != count($bc))
                    $cs.=" | ";
            }

            $CATEGORYTEXT = $xml->createElement('CATEGORYTEXT', $cs);
            $shopitem->appendChild($CATEGORYTEXT);

            $PRODUCTNO = $xml->createElement('PRODUCTNO', $one->id);
            $shopitem->appendChild($PRODUCTNO);

            //----------------PARAMS
            foreach ($one->get_parametersg() as $val) {
                $PARAM = $xml->createElement('PARAM');
                $shopitem->appendChild($PARAM);

                $PARAM_NAME = $xml->createElement('PARAM_NAME', $val->parameter->name);
                $PARAM->appendChild($PARAM_NAME);
                foreach ($one->get_values_to_param($val->parameter) as $valuep) {
                    $VAL = $xml->createElement('VAL', $valuep->parameters_value->value);
                    $PARAM->appendChild($VAL);
                }
            }

            //----------------DELIVERY
            /*  foreach (ORM::factory('payment_method')->get_methods() as $val) {
              $DELIVERY = $xml->createElement('DELIVERY');
              $shopitem->appendChild($DELIVERY);
              $DELIVERY_ID = $xml->createElement('DELIVERY_ID', strtoupper(helper_synctranslit::ToLat(helper_synctranslit::ToLat($val->name))));
              $DELIVERY->appendChild($DELIVERY_ID);

              $DELIVERY_PRICE = $xml->createElement('DELIVERY_PRICE', $val->price);
              $DELIVERY->appendChild($DELIVERY_PRICE);
              } */
        }
        //$product->setAttribute("id", "123");
        //$product->appendChild(new DomAttr('id', '123'));
        $xml->save("feeddata.xml");
        Request::instance()->redirect('/feeddata.xml');
    }

    function xml2assoc(&$xml) {
        $assoc = NULL;
        $n = 0;
        while ($xml->read()) {
            if ($xml->nodeType == XMLReader::END_ELEMENT)
                break;
            if ($xml->nodeType == XMLReader::ELEMENT and ! $xml->isEmptyElement) {
                $assoc[$n]['name'] = $xml->name;
                if ($xml->hasAttributes)
                    while ($xml->moveToNextAttribute())
                        $assoc[$n]['atr'][$xml->name] = $xml->value;
                $assoc[$n]['val'] = self::xml2assoc($xml);
                $n++;
            }
            else if ($xml->isEmptyElement) {
                $assoc[$n]['name'] = $xml->name;
                if ($xml->hasAttributes)
                    while ($xml->moveToNextAttribute())
                        $assoc[$n]['atr'][$xml->name] = $xml->value;
                $assoc[$n]['val'] = "";
                $n++;
            }
            else if ($xml->nodeType == XMLReader::TEXT)
                $assoc = $xml->value;
        }
        return $assoc;
    }

    public function action_re111($lang) {
        $cat1 = 452;
        $cat2 = 543;
        $from = orm::factory("product1")
                ->join("eshop_menus_product1s")
                ->on("product1s.id", "=", "eshop_menus_product1s.product_id")
                ->where("eshop_menus_product1s.eshop_menu_id", "=", $cat1)
                ->group_by("product1s.id")
                ->find_all();
        foreach ($from as $one) {
            echo $one->name . "-" . $one->cz_name . "<br>";
            //  echo $one->cz_name . "<br>";
            $prd = orm::factory("product");
            $prd->language_id = 1;
            $prd->index = orm::factory("product")->last_item_index();
            $prd->articul = $one->articul;
            $prd->name = $one->name;
            $prd->cz_name = $one->cz_name;
            $prd->img = $one->img;
            $prd->price = $one->price;
            $prd->price_old = $one->price_old;
            $prd->price2 = $one->price2;
            $prd->text = $one->text;
            $prd->cz_text = $one->cz_text;
            $prd->quantity = $one->quantity;
            $prd->watch = 1;
            $prd->oid = $one->id;
            $prd->save();

            if ($prd->id) {
                $arr = orm::factory("eshop_menus_product");
                $arr->eshop_menu_id = $cat2;
                $arr->product_id = $prd->id;
                $arr->order = ORM::factory('eshop_menus_product')
                                ->order_by('order', 'desc')
                                ->limit(1)->find()->order + 1;
                $arr->save();
                $engine = new Helper_Catalog_Scanner($prd);
            }
        }
    }

    public function action_re211($lang) {
        $from = orm::factory("products_photo1")
                ->join("products")
                ->on("products.oid", "=", "products_photo1s.product_id")
                ->group_by("products_photo1s.id")
                ->find_all();
        foreach ($from as $one) {
            $prod = orm::factory("product")->where("oid", "=", $one->product_id)->find();
            $photo = orm::factory("products_photo");
            $photo->watch = 1;
            $photo->product_id = $prod->id;
            $photo->img = $one->img;
            $photo->save();
        }
    }

    public function action_re311($lang) {
        $from = orm::factory("product")
                ->where("name_trans", "=", null)
                ->find_all();
        foreach ($from as $one) {
            $one->name_trans = Helper_Utils::str2url($one->name);
            $one->save();
        }
    }

    public function action_hider() {
        $this->auto_render = false;
        $i = 0;
        $c = 0;
        foreach (orm::factory("product")
                ->where("id", ">", 4255)
                ->find_all() as $one) {
            $filename = $one->img; // '/path/to/foo.txt';
            if (file_exists("uploads/products/prw/$filename")) {
                $c++;
            } else {
                $i++;
                $one->watch = 0;
                $one->save();
            }
        }
        echo $c . "-" . $i;
    }

    public function action_fff() {
        foreach (orm::factory("product")
                ->where("img", "=", null)
                ->find_all() as $one) {

            $one->watch = 0;
            $one->save();
        }
    }

    public function action_xml_20() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.sperky-swstyle.cz/fix_boxy.xml");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        $c = orm::factory("product")->last_item_index();
        $cat = 512;
        $k = 1;
        foreach ($xml as $one) {
            $c++;
            $articul = trim($one->KOD);
            $prd = orm::factory("product")
                    ->where("articul", "=", $articul)
                    ->find();
            if (!$prd->id) {
                $prd->language_id = 1;
                $prd->index = $c;
                $prd->articul = $articul;
                $prd->name = $one->NAZEV;
                $prd->text = trim($one->POPIS);
                $prd->short_text = trim($one->POPIS);

                $prd->cz_name = $one->NAZEV;
                $prd->cz_text = trim($one->POPIS);
                $prd->cz_short_text = trim($one->POPIS);
                //   $img = explode("?", $one->IMGURL);
                //  $img = explode("/", $img[0]);
                //   $img = $img[count($img) - 1];
                $prd->imgsrc = $one->IMGURL;
                $prd->price = str_replace(",", ".", $one->MOC_DPH);

                $prd->watch = 1;

                $prd->man = trim($one->VYROBCE);
                $prd->ean = trim($one->EAN);
                $prd->save();
                if ($prd->id) {
                    $d = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $prd->id)
                            ->find();
                    $d->delete();
                    $rel = orm::factory("eshop_menus_product");
                    $rel->product_id = $prd->id;
                    $rel->eshop_menu_id = $cat;
                    $rel->order = ORM::factory('eshop_menus_product')
                                    ->order_by('order', 'desc')
                                    ->limit(1)->find()->order + 1;
                    $rel->save();
                    echo "p_" . $c . "<br>";
                }
            } else {
                /* $d=orm::factory("eshop_menus_product")
                  ->where("product_id","=",$prd->id)
                  ->find();
                  $d->delete();
                  $rel = orm::factory("eshop_menus_product");
                  $rel->product_id = $prd->id;
                  $rel->eshop_menu_id = $cat;
                  $rel->order = ORM::factory('eshop_menus_product')
                  ->order_by('order', 'desc')
                  ->limit(1)->find()->order + 1;
                  $rel->save(); */
                echo "1<br>";
                $engine = new Helper_Catalog_Scanner($prd); //generate breadcrumbs 

                /* $cat = explode("|", trim($one->CATEGORYTEXT));
                  $em = orm::factory("eshop_menu")
                  ->where("name", "=", trim($cat[count($cat) - 1]))
                  ->find();
                  if ($em->id) {
                  $rel = orm::factory("eshop_menus_product")
                  ->where("product_id", "=", $prd->id)
                  ->and_where("eshop_menu_id", "=", $em->id)
                  ->find();
                  if (!$rel->id) {
                  $rel->product_id = $prd->id;
                  $rel->eshop_menu_id = $em->id;
                  $rel->save();
                  }
                  echo "ok<br/>";
                  } else {
                  echo "error<br/>";
                  } */
            }
        }
    }

    public function action_xml_cats() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.zlatnictvitrimama.cz/Katalog-Export-ZboziCz/Vychozi/t845.xml");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        $ar = array();
        foreach ($xml as $one) {
            $ar[] = $one->CATEGORYTEXT;
            // print_r($xml);
            /* echo trim($one->CATEGORYTEXT)."<br>";
              $cat = explode("|", trim($one->CATEGORYTEXT));
              /* $em = orm::factory("eshop_menu")
              ->where("name", "=", trim($cat[count($cat) - 1]))
              ->find(); */
        }

        print_r($ar);
    }

    public function action_xml2710() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.piercing-sperky.cz/xml/export_xml_vo.php?login=Webplanet&pass=29119219");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        $c = 30000;
        $k = 1;

        foreach ($xml as $one) {
            $c++;
            $articul = trim($one->MASTER_KOD);
            $cat = trim($one->CATEGORYTEXT);


            if ($cat == "Náramky" && $c >= 38000 && $c < 40000) {

                $k++;
                $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT->__toString();
                $prd = orm::factory("product")
                        ->where("articul", "=", $articul)
                        ->find();
                if (!$prd->id) {
                    $prd->language_id = 1;
                    $prd->index = $c;
                    $prd->articul = $articul;
                    $prd->name = $one->PRODUCT;
                    $prd->cz_name = $one->PRODUCT;
                    $prd->name_trans = helper_utils::str2url($one->PRODUCT);
                    $img = explode("?", $one->IMGURL);
                    $img = explode("/", $img[0]);
                    $img = $img[count($img) - 1];
                    $prd->imgsrc = $one->IMGURL;
                    $prd->price = str_replace(",", ".", $price);
                    $prd->text = trim($one->DESCRIPTION);
                    //<pro>ženy</pro><material>obecný kov, poocelováno, krystal</material><barva>ocelová, červená</barva><delka>45 cm</delka><typ>s kamínkem</typ><rozmer>lebky 10 mm</rozmer><sire>2.5 mm</sire>
                    //  $st=$one->POPIS->pro.", ".$one->POPIS->material.", ".$one->POPIS->barva.", ".$one->POPIS->delka.", ".$one->POPIS->typ.", ".$one->POPIS->rozmer.", ".$one->POPIS->sire;
                    $ar = array();
                    if ($one->POPIS->pro)
                        $ar[] = $one->POPIS->pro;
                    if ($one->POPIS->material)
                        $ar[] = $one->POPIS->material;
                    if ($one->POPIS->barva)
                        $ar[] = $one->POPIS->barva;
                    if ($one->POPIS->delka)
                        $ar[] = $one->POPIS->delka;
                    if ($one->POPIS->typ)
                        $ar[] = $one->POPIS->typ;
                    if ($one->POPIS->rozmer)
                        $ar[] = $one->POPIS->rozmer;
                    if ($one->POPIS->sire)
                        $ar[] = $one->POPIS->sire;
                    $st = implode(", ", $ar);
                    $prd->short_text = $st;
                    $prd->cz_short_text = $st;
                    $prd->cz_text = $one->DESCRIPTION;
                    $prd->man = trim($one->MANUFACTURER);
                    $prd->watch = 1;
                    $prd->save();
                    // $prd->articul=self::generate_code($prd->id);
                    //  $prd->save();
                    echo "p_" . $c . "<br>";
                } elseif ("a" == "a") {


                    $rel = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $prd->id)
                            ->and_where("eshop_menu_id", "=", 577)
                            ->find();
                    if (!$rel->id) {
                        $rel->product_id = $prd->id;
                        $rel->eshop_menu_id = 577;
                        $rel->save();
                        $engine = new Helper_Catalog_Scanner($prd);
                    }
                    echo "ok<br/>";
                } elseif ("b" == "bс") {
                    $img = explode("?", $one->IMGURL);
                    $img = explode("/", $img[0]);
                    $img = $img[count($img) - 1];
                    $filename = "uploads/products/img/$img";
                    $your_path = "uploads/products/img/$img";
                    $your_path1 = "uploads/products/prw/$img";
                    if (!file_exists($filename)) {
                        echo $c . "imgfail<br/>";
                        copy($one->IMGURL, $your_path);
                        copy($one->IMGURL, $your_path1);
                    } else {

                        echo "ok<br/>";
                    }

                    $img = explode("?", $prd->imgsrc);
                    $img = explode("/", $img[0]);
                    $img = $img[count($img) - 1];
                    $prd->img = $img;
                    $prd->imgsrc = "";

                    $prd->save();
                } elseif ("f" == "fhj") {
                    $ar = array();
                    if ($one->POPIS->pro)
                        $ar[] = $one->POPIS->pro;
                    if ($one->POPIS->material)
                        $ar[] = $one->POPIS->material;
                    if ($one->POPIS->barva)
                        $ar[] = $one->POPIS->barva;
                    if ($one->POPIS->delka)
                        $ar[] = $one->POPIS->delka;
                    if ($one->POPIS->typ)
                        $ar[] = $one->POPIS->typ;
                    if ($one->POPIS->rozmer)
                        $ar[] = $one->POPIS->rozmer;
                    if ($one->POPIS->sire)
                        $ar[] = $one->POPIS->sire;
                    $st = implode(", ", $ar); //$one->POPIS->pro.", ".$one->POPIS->material.", ".$one->POPIS->barva.", ".$one->POPIS->delka.", ".$one->POPIS->typ.", ".$one->POPIS->rozmer.", ".$one->POPIS->sire;
                    $prd->short_text = $st;
                    $prd->cz_short_text = $st;

                    $prd->save();
                }
            }
        }
        echo $k;
    }

    public function action_orders() {
        $e = orm::factory("product")
                ->where("id", ">", 7660)
                ->find_all();
        foreach ($e as $one) {
            $rel = orm::factory("eshop_menus_product")
                    ->where("product_id", "=", $one->id)
                    // ->and_where("eshop_menu_id", "=", 544)
                    ->find();
            //  if (!$rel->id) {
            $rel->product_id = $one->id;
            $rel->eshop_menu_id = 578;
            $rel->save();
            $engine = new Helper_Catalog_Scanner($one);
            // }
        }
    }

    public function action_xm19() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.luuthien.cz/index.php?route=feed%2Fzbozi_base");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        // echo count($xml);
        $c = 0;
        $k = 37785;
        $l = 0;
        foreach ($xml as $one) {
            $c++;

            $articul = trim($one->ITEM_ID);
            if (strpos($one->CATEGORYTEXT, "Ostatní") !== false || strpos($one->CATEGORYTEXT, "Ostatní") !== false) {


                $k++;


                //echo $one->CATEGORYTEXT . "<br>";
                $prd = orm::factory("product")
                        ->where("name", "=", $one->PRODUCT)
                        ->find();
                if (!$prd->id) {

                    $prd->language_id = 1;
                    $prd->index = $k;
                    //  $prd->articul = $one->ITEM_ID;
                    $prd->name = $one->PRODUCT;
                    $prd->cz_name = $one->PRODUCT;

                    $prd->name_trans = helper_utils::str2url($one->PRODUCT);
                    $prd->imgsrc = $one->IMGURL;
                    // $prd->img = $img;
                    $price = (double) str_replace(",", ".", $one->PRICE_VAT);
                    $price = $price + round($price * 0.25);
                    $prd->price = $price;
                    $prd->text = trim($one->DESCRIPTION);
                    //  $prd->manufacturer = trim($one->MANUFACTURER);
                    $prd->short_text = trim($one->DESCRIPTION);
                    $prd->watch = 1;
                    $prd->save();
                } else {
                    echo "dd<br>";
                    //$prd->name_trans = helper_utils::str2url($one->PRODUCT);
                    //$prd->cz_name = $one->PRODUCT;
                    //$prd->save();
                    $rel = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $prd->id)
                            ->and_where("eshop_menu_id", "=", 590)
                            ->find();
                    if (!$rel->id) {
                        $rel->product_id = $prd->id;
                        $rel->eshop_menu_id = 590;
                        $rel->save();
                        $engine = new Helper_Catalog_Scanner($prd);
                    }
                }
            }
        }
        echo $l;
    }

    public function action_xm20() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.piercing-sperky.cz/xml/export_xml_vo.php?login=Webplanet&pass=29119219");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        // echo count($xml);
        $c = 0;
        $k = 37785;
        $l = 0;
        $index = orm::factory("product")->order_by("index", "desc")->find()->index + 20;
        foreach ($xml as $one) {
            if ($one->CATEGORYTEXT == "Řetízky") {
                // print_r($one->VELIKOSTI->VELIKOST);
                $oid = trim($one->ID);
                $articul = trim($one->MASTER_KOD);
                $name = trim($one->PRODUCT);
                $man = trim($one->MANUFACTURER);
                $text = trim($one->DESCRIPTION);
                $img = trim($one->IMGURL_BIG);
                $count = count($one->VELIKOSTI->VELIKOST);
                $product = orm::factory("product")
                        ->where("name", "=", $name)
                        ->or_where("articul", "=", $articul)
                        ->find();
                if (!$product->id) {
                    $index++;
                    $product->oid = $oid;
                    $product->articul = $articul;
                    $product->name = $name;
                    $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                    $product->price = $price;
                    $product->index = $index;
                    $product->save();
                    if ($count == 1) {
                        
                    } else {

                        foreach ($one->VELIKOSTI->VELIKOST as $col) {
                            if ($one->PARAMETR == "barva") {
                                $color = orm::factory("color")
                                        ->where("name", "=", $col->HODNOTA)
                                        ->find();
                                if (!$color->id) {
                                    $color->name = $col->HODNOTA;
                                    $color->save();
                                }
                                $rel = orm::factory("products_color");
                                $rel->product_id = $product->id;
                                $rel->color_id = $color->id;
                                $rel->price = $col->PRICE_VAT;
                                $rel->save();
                            }
                        }
                    }
                    $rel = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $product->id)
                            ->and_where("eshop_menu_id", "=", 601)
                            ->find();
                    if (!$rel->id) {
                        $rel->product_id = $product->id;
                        $rel->eshop_menu_id = 601;
                        $rel->save();
                        $engine = new Helper_Catalog_Scanner($product);
                    }
                } else {
                    $product->oid = $oid;
                    $product->articul = $articul;
                    $product->name = $name;
                    //     $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                    $product->price = $price;
                    //   $product->index = $index;
                    $product->save();
                }
                //    print_r($one->VELIKOSTI->VELIKOST);

                echo "<br><br>";
            }
            //  echo $one->CATEGORYTEXT."<br>";
            if ($c > 100000) {
                break;
            }
            $c++;
        }

        echo $l;
    }

    public function action_xm21() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.piercing-sperky.cz/xml/export_xml_vo.php?login=Webplanet&pass=29119219");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        // echo count($xml);
        $c = 0;
        $k = 37785;
        $l = 0;
        $index = orm::factory("product")->order_by("index", "desc")->find()->index + 20;
        foreach ($xml as $one) {
            if ($one->CATEGORYTEXT == "Přívěsky") {
                // print_r($one->VELIKOSTI->VELIKOST);
                $oid = trim($one->ID);
                $articul = trim($one->MASTER_KOD);
                $name = trim($one->PRODUCT);
                $man = trim($one->MANUFACTURER);
                $text = trim($one->DESCRIPTION);
                $img = trim($one->IMGURL_BIG);
                $count = count($one->VELIKOSTI->VELIKOST);
                $product = orm::factory("product")
                        ->where("name", "=", $name)
                        ->or_where("articul", "=", $articul)
                        ->find();
                if (!$product->id) {
                    $index++;
                    $product->oid = $oid;
                    $product->articul = $articul;
                    $product->name = $name;
                    $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                    $product->price = $price;
                    $product->index = $index;
                    $product->save();
                    if ($count == 1) {
                        
                    } else {

                        foreach ($one->VELIKOSTI->VELIKOST as $col) {
                            if ($one->PARAMETR == "barva") {
                                $color = orm::factory("color")
                                        ->where("name", "=", $col->HODNOTA)
                                        ->find();
                                if (!$color->id) {
                                    $color->name = $col->HODNOTA;
                                    $color->save();
                                }
                                $rel = orm::factory("products_color");
                                $rel->product_id = $product->id;
                                $rel->color_id = $color->id;
                                $rel->price = $col->PRICE_VAT;
                                $rel->save();
                            }
                        }
                    }
                    $rel = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $product->id)
                            ->and_where("eshop_menu_id", "=", 600)
                            ->find();
                    if (!$rel->id) {
                        $rel->product_id = $product->id;
                        $rel->eshop_menu_id = 600;
                        $rel->save();
                        $engine = new Helper_Catalog_Scanner($product);
                    }
                } else {
                    $product->oid = $oid;
                    $product->articul = $articul;
                    $product->name = $name;
                    //     $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                    $product->price = $price;
                    //   $product->index = $index;
                    $product->save();
                }
                //    print_r($one->VELIKOSTI->VELIKOST);

                echo "<br><br>";
            }
            //  echo $one->CATEGORYTEXT."<br>";
            if ($c > 100000) {
                break;
            }
            $c++;
        }

        echo $l;
    }

    public function action_xm22() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.piercing-sperky.cz/xml/export_xml_vo.php?login=Webplanet&pass=29119219");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        // echo count($xml);
        $c = 0;
        $k = 37785;
        $l = 0;
        $index = orm::factory("product")->order_by("index", "desc")->find()->index + 20;
        foreach ($xml as $one) {
            if ($one->CATEGORYTEXT == "Dětské náušnice") {
                // print_r($one->VELIKOSTI->VELIKOST);
                $oid = trim($one->ID);
                $articul = trim($one->MASTER_KOD);
                $name = trim($one->PRODUCT);
                $man = trim($one->MANUFACTURER);
                $text = trim($one->DESCRIPTION);
                $img = trim($one->IMGURL_BIG);
                $count = count($one->VELIKOSTI->VELIKOST);
                $product = orm::factory("product")
                        ->where("name", "=", $name)
                        ->or_where("articul", "=", $articul)
                        ->find();
                if (!$product->id) {
                    $index++;
                    $product->oid = $oid;
                    $product->articul = $articul;
                    $product->name = $name;
                    $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                    $product->price = $price;
                    $product->index = $index;
                    $product->save();
                    if ($count == 1) {
                        
                    } else {

                        foreach ($one->VELIKOSTI->VELIKOST as $col) {
                            if ($one->PARAMETR == "barva") {
                                $color = orm::factory("color")
                                        ->where("name", "=", $col->HODNOTA)
                                        ->find();
                                if (!$color->id) {
                                    $color->name = $col->HODNOTA;
                                    $color->save();
                                }
                                $rel = orm::factory("products_color");
                                $rel->product_id = $product->id;
                                $rel->color_id = $color->id;
                                $rel->price = $col->PRICE_VAT;
                                $rel->save();
                            }
                        }
                    }
                    $rel = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $product->id)
                            ->and_where("eshop_menu_id", "=", 524)
                            ->find();
                    if (!$rel->id) {
                        $rel->product_id = $product->id;
                        $rel->eshop_menu_id = 524;
                        $rel->save();
                        $engine = new Helper_Catalog_Scanner($product);
                    }
                } else {
                    $product->oid = $oid;
                    $product->articul = $articul;
                    $product->name = $name;
                    //     $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                    $product->price = $price;
                    //   $product->index = $index;
                    $product->save();
                }
                //    print_r($one->VELIKOSTI->VELIKOST);

                echo "<br><br>";
            }
            //  echo $one->CATEGORYTEXT."<br>";
            if ($c > 100000) {
                break;
            }
            $c++;
        }

        echo $l;
    }

    public function action_xm2111() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.piercing-sperky.cz/xml/export_xml_vo.php?login=Webplanet&pass=29119219");
// echo $xmlString;
        $xml = new SimpleXMLElement($xmlString);
        // echo count($xml);
        $c = 0;
        $k = 37785;
        $l = 0;
        $index = orm::factory("product")->order_by("index", "desc")->find()->index + 20;
        foreach ($xml as $one) {
            if ($one->CATEGORYTEXT == "Řetízky") {
                // print_r($one->VELIKOSTI->VELIKOST);
                $oid = trim($one->ID);
                $articul = trim($one->MASTER_KOD);
                $name = trim($one->PRODUCT);
                $man = trim($one->MANUFACTURER);
                $text = trim($one->DESCRIPTION);
                $img = trim($one->IMGURL_BIG);
                $count = count($one->VELIKOSTI->VELIKOST);
                $product = orm::factory("product")
                        ->where("name", "=", $name)
                        ->or_where("articul", "=", $articul)
                        ->find();
            }
            echo $one->CATEGORYTEXT . "<br>" . $one->IMGURL_BIG . "<br>" . $one->PRODUCT;
            //    print_r($one->VELIKOSTI->VELIKOST);

            echo "<br><br>";
            //  echo $one->CATEGORYTEXT."<br>";
            if ($c > 10000) {
                break;
            }
            $c++;
        }

        echo $l;
    }

    public function action_sync12122014_11($lang) {
        $this->auto_render = FALSE;
        $prods = orm::factory("product")
                ->join("menus_products")
                ->on("products.id", "=", "menus_products.product_id")
                ->where("menus_products.menu_id", "=", 43)
                ->group_by("products.id")
                ->find_all();
        foreach ($prods as $one) {
            $price = round($one->price / 10);
            $one->price_old = $one->price;
            $one->price = $one->price - $price;
            $one->save();
        }
    }

    public function action_xm25122014() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.odvarkabijoux.com/_tmp/feed/1_feed.xml");
// echo $xmlString;
        $xml = new SimpleXMLElement(trim($xmlString));
        // echo count($xml);
        $c = 0;
        $k = 37785;
        $l = 0;
        $index = orm::factory("product")->order_by("index", "desc")->find()->index + 20;
        echo count($xml);
        foreach ($xml as $one) {
            if (strpos($one->CATEGORYTEXT, 'náramky') !== false && ($c > 0 && $c < 15000)) {
                //    if ($one->CATEGORYTEXT == "Přívěsky") {
                // print_r($one->VELIKOSTI->VELIKOST);
                //$oid = trim($one->ID);
                // $articul = trim($one->MASTER_KOD);
                $name = trim($one->PRODUCT);
                //  $man = trim($one->MANUFACTURER);
                $text = trim($one->DESCRIPTION);
                $img = trim($one->IMGURL);
                $price = $one->PRICE_VAT;
                $CATEGORYTEXT = count($one->CATEGORYTEXT);
                $product = orm::factory("product")
                        ->where("name", "=", $name)
                        //  ->or_where("articul", "=", $articul)
                        ->find();
                if (!$product->id) {
                    $index++;
                    //  $product->oid = $oid;
                    //  $product->articul = $articul;
                    $product->name = $name;
                    $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    // $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $product->price = $price;
                    $product->index = $index;
                    $product->save();
                    if ($count == 1) {
                        
                    } else {

                        /*  foreach ($one->VELIKOSTI->VELIKOST as $col) {
                          if ($one->PARAMETR == "barva") {
                          $color = orm::factory("color")
                          ->where("name", "=", $col->HODNOTA)
                          ->find();
                          if (!$color->id) {
                          $color->name = $col->HODNOTA;
                          $color->save();
                          }
                          $rel = orm::factory("products_color");
                          $rel->product_id = $product->id;
                          $rel->color_id = $color->id;
                          $rel->price = $col->PRICE_VAT;
                          $rel->save();
                          }
                          } */
                    }
                    $rel = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $product->id)
                            ->and_where("eshop_menu_id", "=", 577)
                            ->find();
                    if (!$rel->id) {
                        $rel->product_id = $product->id;
                        $rel->eshop_menu_id = 577;
                        $rel->save();
                        $engine = new Helper_Catalog_Scanner($product);
                    }
                } else {
                    $product->price = $price;
                    $product->save();
                    /*  $product->oid = $oid;
                      $product->articul = $articul;
                      $product->name = $name;
                      //     $product->name_trans = helper_utils::str2url($name);
                      $product->cz_name = $name;
                      $product->text = $text;
                      $product->cz_text = $text;
                      $product->man = $man;
                      $product->imgsrc = $img;
                      $product->language_id = 1;
                      $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                      $product->price = $price;
                      //   $product->index = $index;
                      $product->save(); */
                }
                //    print_r($one->VELIKOSTI->VELIKOST);

                echo "<br><br>";
            }
            //  echo $one->CATEGORYTEXT."<br>";
            if ($c > 100000) {
                break;
            }
            $c++;
        }

        echo $l;
    }

    public function action_xm28012015() {
        $this->auto_render = false;

        $xmlString = file_get_contents("http://www.stoklasa.cz/xmlfeed-cesko-bal.xml");
// echo $xmlString;
        $xml = new SimpleXMLElement(trim($xmlString));
        // echo count($xml);
        $c = 0;
        $k = 37785;
        $l = 0;
        $index = orm::factory("product")->order_by("index", "desc")->find()->index + 20;
        foreach ($xml as $one) {
            $c++;
            if ($c < 100) {
                print_r($one);
            }
        }
        foreach ($xml111 as $one) {
            if (strpos($one->CATEGORYTEXT, 'náramky') !== false && ($c > 0 && $c < 15000)) {
                //    if ($one->CATEGORYTEXT == "Přívěsky") {
                // print_r($one->VELIKOSTI->VELIKOST);
                //$oid = trim($one->ID);
                // $articul = trim($one->MASTER_KOD);
                $name = trim($one->PRODUCT);
                //  $man = trim($one->MANUFACTURER);
                $text = trim($one->DESCRIPTION);
                $img = trim($one->IMGURL);
                $price = $one->PRICE_VAT;
                $CATEGORYTEXT = count($one->CATEGORYTEXT);
                $product = orm::factory("product")
                        ->where("name", "=", $name)
                        //  ->or_where("articul", "=", $articul)
                        ->find();
                if (!$product->id) {
                    $index++;
                    //  $product->oid = $oid;
                    //  $product->articul = $articul;
                    $product->name = $name;
                    $product->name_trans = helper_utils::str2url($name);
                    $product->cz_name = $name;
                    $product->text = $text;
                    $product->cz_text = $text;
                    // $product->man = $man;
                    $product->imgsrc = $img;
                    $product->language_id = 1;
                    $product->price = $price;
                    $product->index = $index;
                    $product->save();
                    if ($count == 1) {
                        
                    } else {

                        /*  foreach ($one->VELIKOSTI->VELIKOST as $col) {
                          if ($one->PARAMETR == "barva") {
                          $color = orm::factory("color")
                          ->where("name", "=", $col->HODNOTA)
                          ->find();
                          if (!$color->id) {
                          $color->name = $col->HODNOTA;
                          $color->save();
                          }
                          $rel = orm::factory("products_color");
                          $rel->product_id = $product->id;
                          $rel->color_id = $color->id;
                          $rel->price = $col->PRICE_VAT;
                          $rel->save();
                          }
                          } */
                    }
                    $rel = orm::factory("eshop_menus_product")
                            ->where("product_id", "=", $product->id)
                            ->and_where("eshop_menu_id", "=", 577)
                            ->find();
                    if (!$rel->id) {
                        $rel->product_id = $product->id;
                        $rel->eshop_menu_id = 577;
                        $rel->save();
                        $engine = new Helper_Catalog_Scanner($product);
                    }
                } else {
                    $product->price = $price;
                    $product->save();
                    /*  $product->oid = $oid;
                      $product->articul = $articul;
                      $product->name = $name;
                      //     $product->name_trans = helper_utils::str2url($name);
                      $product->cz_name = $name;
                      $product->text = $text;
                      $product->cz_text = $text;
                      $product->man = $man;
                      $product->imgsrc = $img;
                      $product->language_id = 1;
                      $price = $one->VELIKOSTI->VELIKOST->PRICE_VAT;
                      $product->price = $price;
                      //   $product->index = $index;
                      $product->save(); */
                }
                //    print_r($one->VELIKOSTI->VELIKOST);

                echo "<br><br>";
            }
            //  echo $one->CATEGORYTEXT."<br>";
            if ($c > 100000) {
                break;
            }
            $c++;
        }

        echo $l;
    }

    public function action_czvls() {
        $this->auto_render = false;
        foreach (orm::factory("parameters_value")->find_all() as $one) {
            $one->cz_value = $one->value;
            $one->save();
        }
    }

    public function action_facturas($lang) {
        $this->auto_render = false;
        $o = orm::factory("order")
                ->where("orders_status_id", "=", 5)
                ->find_all();
        $str = "";
        foreach ($o as $one) {
            $str.="<a target='blank' href='" . url::base() . "cart/factura/" . $one->id . "'>" . url::base() . "cart/factura/" . $one->id . "</a><br>";
        }
        echo $str;
        die();
    }

    public function action_imagehelp($limit = 100, $offset = 0) {
        $this->auto_render = false;
        $p = orm::factory("product")
                ->limit($limit)
                ->offset($offset)
                ->find_all();
        foreach ($p as $one) {
            if ($one->img) {
                //  echo $one->name . "-" . $one->img . "<br>";
                $p = "uploads/products/img/" . $one->img;
                $p2 = "uploads/products/img2/" . $one->img;
                if (file_exists($p)) {
                    //  echo "norm<br>";
                    $i = self::crp($p, $p2);
                } else {
                    // echo "not ex<br>";
                }
            }
        }
        $offset = $offset + 100;
        //   Request::instance()->redirect("/welcome/imagehelp/$limit/".($offset+100));
        header('Location: ' . url::base() . "welcome/imagehelp/$limit/$offset");
        die();
    }

    function crp($file, $file2) {
        // Размеры, до которых будем обрезать
        $size_width = 648;
        $size_height = 365;
// Открываем изображение
        $image = Image::factory($file);
// Подсчитываем соотношение сторон картинки
        $ratio = $image->width / $image->height;
// Соотношение сторон нужных размеров
        $original_ratio = $size_width / $size_height;
// Размеры, до которых обрежем картинку до масштабирования
        $crop_width = $image->width;
        $crop_height = $image->height;
// Смотрим соотношения
        if ($ratio > $original_ratio) {
            // Если ширина картинки слишком большая для пропорции,
            // то будем обрезать по ширине
            $crop_width = round($original_ratio * $crop_height);
        } else {
            // Либо наоборот, если высота картинки слишком большая для пропорции,
            // то обрезать будем по высоте
            $crop_height = round($crop_width / $original_ratio);
        }
        if ($image->width > $size_width || $image->height > $size_height) {
// Обрезаем по высчитанным размерам до нужной пропорции
            // $image->crop($crop_width, $crop_height);
// Масштабируем картинку то точных размеров
            $image->resize($size_width, $size_height);
        }
        $image->save($file2);
        // } else {

        $im = imagecreatetruecolor($size_width, $size_height);

        $white = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $white);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        //  $is = imagecreatefromjpeg("/".$file2);
        $is = self::imagecreatefromfile($file2);
        if (!$is) {
            return;
            //    echo "wfwefwefeeeeeeeeeeeeeeeee";
        }
        $x = ($size_width - $image->width) / 2;
        $y = ($size_height - $image->height) / 2;
        // echo $x . "-" . $y;
        imagecopy($im, $is, $x, $y, 0, 0, imagesx($is), imagesy($is));
        imagefill($im, 0, 0, $white);
        // header("Content-type: image/png");
        $handlers = array(
            'jpg' => 'imagejpeg',
            'jpeg' => 'imagejpeg',
            'png' => 'imagepng',
            'gif' => 'imagegif'
        );
        $extension = strtolower(substr($file2, strrpos($file2, '.') + 1));
        //  echo $extension . "-wefwef<br>";
        if ($handler = $handlers[$extension]) {
            $image = $handler($im, $file2);
            //do the rest of your thumbnail stuff here
        } else {
            //throw an 'invalid image' error
        }
        //  imagepng($im, $file2);
        imagedestroy($im);
        // }
// Сохраняем изображение в файл

        return $image;
    }

    function imagecreatefromfile($filename) {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException('File "' . $filename . '" not found.');
        }
        switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($filename);
                break;

            case 'png':
                return imagecreatefrompng($filename);
                break;

            case 'gif':
                return imagecreatefromgif($filename);
                break;

            default:
                // throw new InvalidArgumentException('File "' . $filename . '" is not valid jpg, png or gif image.');
                break;
        }
    }

    public function action_txt() {
        // header('Content-Type: text/html; charset=utf-8');
        $this->auto_render = false;

        $content = "";
        /*  foreach (ORM::factory('product')
          ->join("eshop_menus_products")
          ->on("eshop_menus_products.product_id", "=", "products.id")
          ->join("eshop_menus")
          ->on("eshop_menus.id", "=", "eshop_menus_products.eshop_menu_id")
          ->where("products.watch", "=", 1)
          ->and_where("eshop_menus.watch", "=", 1)
          ->group_by("products.id")
          ->find_all() as $key => $one) {
          $content.=$one->id . "::" . $one->name . "::" . $one->price . " грн\n";
          } */
        foreach (ORM::factory('settings_value')
                ->where("settings_name_id", "=", 31)
                ->find_all() as $key => $one) {
            $content.=$one->value . "\n";
        }
        $r = self::writeUTF8File("mails.csv", $content);
        /*   $fp = fopen("prices.txt", "wb");
          //   fwrite($fp, $content);
          file_put_contents("prices.txt", "\xEF\xBB\xBF" . $content);
          fclose($fp); */
        Request::instance()->redirect("mails.csv");
    }

    public function action_txtorders() {
        // header('Content-Type: text/html; charset=utf-8');
        $this->auto_render = false;

        $content = "";
        /*  foreach (ORM::factory('product')
          ->join("eshop_menus_products")
          ->on("eshop_menus_products.product_id", "=", "products.id")
          ->join("eshop_menus")
          ->on("eshop_menus.id", "=", "eshop_menus_products.eshop_menu_id")
          ->where("products.watch", "=", 1)
          ->and_where("eshop_menus.watch", "=", 1)
          ->group_by("products.id")
          ->find_all() as $key => $one) {
          $content.=$one->id . "::" . $one->name . "::" . $one->price . " грн\n";
          } */
        foreach (ORM::factory('order')
                ->group_by("orders.email")
                ->find_all() as $key => $one) {
            $content.=$one->email . "\n";
        }
        $r = self::writeUTF8File("ordersmails.csv", $content);
        /*   $fp = fopen("prices.txt", "wb");
          //   fwrite($fp, $content);
          file_put_contents("prices.txt", "\xEF\xBB\xBF" . $content);
          fclose($fp); */
        Request::instance()->redirect("ordersmails.csv");
    }

    function writeUTF8File($filename, $content) {
        $f = fopen($filename, "w");
        # Now UTF-8 - Add byte order mark 
        fwrite($f, pack("CCC", 0xef, 0xbb, 0xbf));
        fwrite($f, $content);
        fclose($f);
    }

    public function action_torders() {
        //CAST(SUM(products_parameters.value) as DECIMAL(9,2))
        $this->auto_render = false;
        //+IFNULL(SUM(products_parameters.value), 0)
        $f = DB::select(DB::Expr("orders.name,orders.code, (SUM(orders_products.price*orders_products.quantity) + payment_methods.price + payu_methods.price + IFNULL(SUM(b.amount*orders_products.quantity), 0)) as sume"))
                        ->from("orders")
                        ->join("orders_products")
                        ->on("orders_products.order_id", "=", "orders.id")
                        ->join("payment_methods")
                        ->on("payment_methods.id", "=", "orders.payment_method_id")
                        ->join("payu_methods")
                        ->on("payu_methods.id", "=", "orders.payu_method_id")
                        ->join(
                                DB::expr("(SELECT id,orders_product_id,
                   SUM(price) AS amount
         FROM orders_products_parameters
         GROUP BY orders_product_id) AS b"), "left")
                        ->on("orders_products.id", "=", "b.orders_product_id")
                        // ->join("products_parameters", "left")
                        // ->on("orders_products_parameters.parameter_id", "=", "products_parameters.parameter_id")
                        //->on("orders_products_parameters.orders_product_id", "=", "products_parameters.product_id")
                        //  ->join("products_parameters", "left")
                        //  ->on("orders_products.product_id", "=", "products_parameters.product_id")
                        ->order_by("orders.id", "desc")
                        ->group_by("orders.id")
                        // ->limit(1500)
                        ->execute()->as_array();
        $content.="order code;order name;order price\n";
        $sum = 0;
        foreach ($f as $key => $one) {
            $content.=$one["code"] . ";" . $one["name"] . ";" . $one["sume"] . "Kč \n";
            $sum = $sum + $one["sume"];
        }
        $content.="FINAL SUME:" . $sum . "Kč ";
        $r = self::writeUTF8File("orders.txt", $content);
        /*   $fp = fopen("prices.txt", "wb");
          //   fwrite($fp, $content);
          file_put_contents("prices.txt", "\xEF\xBB\xBF" . $content);
          fclose($fp); */
        Request::instance()->redirect("orders.txt");
        //echo $content;
        //print_r($f);
    }

    public function action_fixop() {
        $this->auto_render = false;
        $o = orm::factory("order")
                ->limit(3000)
                ->offset(18000)
                ->order_by("id", "asc")
                ->find_all();
        foreach ($o as $one) {
            foreach ($one->orders_products->find_all() as $two) {
                $two->name = $two->product->name;
                $two->articul = $two->product->articul;
                $two->save();
                foreach ($two->orders_products_parameters->find_all() as $param) {
                    $v2 = orm::factory("products_parameter")
                            ->where("product_id", "=", $param->orders_product->product_id)
                            ->and_where("parameter_id", "=", $param->parameter_id)
                            ->and_where("parameters_value_id", "=", $param->parameters_value_id)
                            ->find();
                    $param->price = $v2->value;
                    $param->name = $v2->parameter->name . ": " . $v2->parameters_value->value;
                    $param->save();
                }
            }
        }
    }

    public function action_xml5($lang) {
        $this->auto_render = false;
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $shop = $xml->createElement('shop');
        $xml->appendChild($shop);
        $products = DB::select(DB::expr("products.*,eshop_menus.cz_name as esname,eshop_menus.id as esid"))
                ->from("products")
                //  orm::factory("product")
                ->join("eshop_menus_products", "left")
                ->on("eshop_menus_products.product_id", "=", "products.id")
                ->join("eshop_menus", "left")
                ->on("eshop_menus_products.eshop_menu_id", "=", "eshop_menus.id")
                ->where('products.watch', '=', 1)
                ->and_where('eshop_menus.watch', '=', 1)
                ->and_where("products.breadcrumb", "!=", null)
                ->and_where('products.language_id', '=', 1)
                ->and_where('products.price', '>', 0)
                ->group_by("products.cz_name")
                ->execute();
        foreach ($products as $key => $one) {
            $shopitem = $xml->createElement('shopitem');
            $shop->appendChild($shopitem);
            $ITEM_ID = $xml->createElement('ITEM_ID', $one["id"]);
            $shopitem->appendChild($ITEM_ID);

            $ITEMGROUP_ID = $xml->createElement('ITEMGROUP_ID', $one["esid"]);
            $shopitem->appendChild($ITEMGROUP_ID);

            $PRODUCTNAME = $xml->createElement('PRODUCTNAME', $one["name"] . " " . $one["articul"]);
            $shopitem->appendChild($PRODUCTNAME);
            $PRODUCT = $xml->createElement('PRODUCT', $one["name"]);
            $shopitem->appendChild($PRODUCT);
            // $t=  html_entity_decode(strip_tags($one["cz_text"]));
            $DESCRIPTION = $xml->createElement('DESCRIPTION', $one["name"]);
            $shopitem->appendChild($DESCRIPTION);

            $DELIVERY_DATE = $xml->createElement('DELIVERY_DATE', '25');
            $shopitem->appendChild($DELIVERY_DATE);

            $URL = $xml->createElement('URL', Helper_Urls::product($one));
            $shopitem->appendChild($URL);
            $photo = $one;
            if ($photo["img"]) {
                $IMGURL = $xml->createElement('IMGURL', url::base() . "uploads/products/img/" . $photo["img"]);
                $shopitem->appendChild($IMGURL);
                $IMGURL_ALTERNATIVE = $xml->createElement('IMGURL_ALTERNATIVE', url::base() . "uploads/products/img/" . $photo["img"]);
                $shopitem->appendChild($IMGURL_ALTERNATIVE);
            } elseif ($one["imgsrc"]) {
                $IMGURL = $xml->createElement('IMGURL', $one["imgsrc"]);
                $shopitem->appendChild($IMGURL);
                $IMGURL_ALTERNATIVE = $xml->createElement('IMGURL_ALTERNATIVE', $one["imgsrc"]);
                $shopitem->appendChild($IMGURL_ALTERNATIVE);
            }
            $EAN = $xml->createElement('EAN', self::generateEAN($one["id"]));
            $shopitem->appendChild($EAN);
            /* $photos1 = $one["products_photos->find_all(); */

            $photos1 = orm::factory("products_photo")
                    ->where("product_id", "=", $one["id"])
                    ->find_all();
            if (count($photos1) > 0) {
                //  $photos = $xml->createElement('PHOTOS');
                //  $shopitem->appendChild($photos);
            }
            foreach ($photos1 as $p) {
                if ($p->img) {
                    $IMGURL1 = $xml->createElement('IMGURL_ALTERNATIVE', url::base() . "uploads/products/img/" . $p->img);
                    $shopitem->appendChild($IMGURL1);
                } elseif ($p->imgsrc) {
                    $IMGURL1 = $xml->createElement('IMGURL_ALTERNATIVE', $p->imgsrc);
                    $shopitem->appendChild($IMGURL1);
                }
            }
            //  $EAN = $xml->createElement('EAN', $one["articul"]);
            //    $shopitem->appendChild($EAN); 
            $PRICE_VAT = $xml->createElement('PRICE_VAT', helper_utils::get_price($one, true));
            $shopitem->appendChild($PRICE_VAT);

            $MANUFACTURER = $xml->createElement('MANUFACTURER', "vsechnozciny");
            $shopitem->appendChild($MANUFACTURER);

            $ITEM_TYPE = $xml->createElement('ITEM_TYPE', "new");
            $shopitem->appendChild($ITEM_TYPE);

            $EXTRA_MESSAGE = $xml->createElement('EXTRA_MESSAGE', "free_delivery");
            $shopitem->appendChild($EXTRA_MESSAGE);


            $CATEGORYTEXT = $xml->createElement('CATEGORYTEXT', $one["esname"]);
            $shopitem->appendChild($CATEGORYTEXT);

            $PRODUCTNO = $xml->createElement('PRODUCTNO', $one["id"]);
            $shopitem->appendChild($PRODUCTNO);

            //----------------PARAMS
            $prms = orm::factory("products_parameter")
                    ->where("product_id", "=", $one["id"])
                    ->group_by("product_id")
                    ->find_all();
            foreach ($prms as $val) {
                $PARAM = $xml->createElement('PARAM');
                $shopitem->appendChild($PARAM);

                $PARAM_NAME = $xml->createElement('PARAM_NAME', $val->parameter->name);
                $PARAM->appendChild($PARAM_NAME);
                $values = ORM::factory('products_parameter')
                                ->where('product_id', '=', $one["id"])
                                ->and_where('parameter_id', '=', $val->parameter_id)->find_all();
                $out = array();
                foreach ($values as $value) {
                    if ($value->parameters_value->value) {
                        $out[] = $value;
                    }
                }
                foreach ($values as $valuep) {
                    $VAL = $xml->createElement('VAL', $valuep->parameters_value->value);
                    $PARAM->appendChild($VAL);
                }
            }

            //----------------DELIVERY
            /*  foreach (ORM::factory('payment_method')->get_methods() as $val) {
              $DELIVERY = $xml->createElement('DELIVERY');
              $shopitem->appendChild($DELIVERY);
              $DELIVERY_ID = $xml->createElement('DELIVERY_ID', strtoupper(helper_synctranslit::ToLat(helper_synctranslit::ToLat($val->name))));
              $DELIVERY->appendChild($DELIVERY_ID);

              $DELIVERY_PRICE = $xml->createElement('DELIVERY_PRICE', $val->price);
              $DELIVERY->appendChild($DELIVERY_PRICE);
              } */
        }
        //$product->setAttribute("id", "123");
        //$product->appendChild(new DomAttr('id', '123'));
        $xml->save("feeddata4.xml");
        Request::instance()->redirect('/feeddata4.xml');
    }

    public function action_xml4($lang) {
        $this->auto_render = false;
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $shop = $xml->createElement('SERVER');
        $xml->appendChild($shop);
        $products = DB::select(DB::expr("products.*,eshop_menus.cz_name as esname,eshop_menus.id as esid"))
                ->from("products")
                //  orm::factory("product")
                ->join("eshop_menus_products", "left")
                ->on("eshop_menus_products.product_id", "=", "products.id")
                ->join("eshop_menus", "left")
                ->on("eshop_menus_products.eshop_menu_id", "=", "eshop_menus.id")
                ->where('products.watch', '=', 1)
                ->and_where('eshop_menus.watch', '=', 1)
                ->and_where("products.breadcrumb", "!=", null)
                ->and_where('products.language_id', '=', 1)
                ->and_where('products.price', '>', 0)
                ->group_by("products.cz_name")
                ->execute();
        foreach ($products as $key => $one) {
            $shopitem = $xml->createElement('DEAL');
            $shop->appendChild($shopitem);
            $ITEM_ID = $xml->createElement('ID', $one["id"]);
            $shopitem->appendChild($ITEM_ID);


            $LANGUAGE = $xml->createElement('LANGUAGE', "CS");
            $shopitem->appendChild($LANGUAGE);

            $PROVIDER = $xml->createElement('PROVIDER');
            $shopitem->appendChild($PROVIDER);

            $PROVIDER_TITLE = $xml->createElement('PROVIDER_TITLE', 'vsechnozciny');
            $PROVIDER->appendChild($PROVIDER_TITLE);

            $PROVIDER_ADDRESS = $xml->createElement('PROVIDER_ADDRESS', 'Štiková 5, 32300 Plzeň');
            $PROVIDER->appendChild($PROVIDER_ADDRESS);

            $PROVIDER_WEB = $xml->createElement('PROVIDER_WEB', url::base());
            $PROVIDER->appendChild($PROVIDER_WEB);

            $PROVIDER_EMAIL = $xml->createElement('PROVIDER_EMAIL', 'info@vsechnozciny.cz');
            $PROVIDER->appendChild($PROVIDER_EMAIL);

            $PROVIDER_PHONE = $xml->createElement('PROVIDER_PHONE', '+420 123 456 789');
            $PROVIDER->appendChild($PROVIDER_PHONE);

            $PROVIDER_GPS_LAT = $xml->createElement('PROVIDER_GPS_LAT', '49.778511');
            $PROVIDER->appendChild($PROVIDER_GPS_LAT);

            $PROVIDER_GPS_LNG = $xml->createElement('PROVIDER_GPS_LNG', '13.382440');
            $PROVIDER->appendChild($PROVIDER_GPS_LNG);

            $CITIES = $xml->createElement('CITIES');
            $shopitem->appendChild($CITIES);

            $CITY = $xml->createElement('CITY', 'Praha');
            $CITIES->appendChild($CITY);

            $TAGS = $xml->createElement('TAGS');
            $shopitem->appendChild($TAGS);

            $TAG = $xml->createElement('TAG', $one["name"]);
            $TAGS->appendChild($TAG);

            $TITLE_SHORT = $xml->createElement('TITLE_SHORT', $one["name"]);
            $shopitem->appendChild($TITLE_SHORT);

            $TITLE = $xml->createElement('TITLE', $one["name"] . " " . $one["articul"]);
            $shopitem->appendChild($TITLE);

            $URL = $xml->createElement('URL', Helper_Urls::product($one));
            $shopitem->appendChild($URL);

            $IMAGES = $xml->createElement('IMAGES');
            $shopitem->appendChild($IMAGES);
            $photo = $one;
            if ($photo["img"]) {
                $IMGURL = $xml->createElement('IMAGE', url::base() . "uploads/products/img/" . $photo["img"]);
                $IMAGES->appendChild($IMGURL);
            } elseif ($one["imgsrc"]) {
                $IMGURL = $xml->createElement('IMAGE', $one["imgsrc"]);
                $IMAGES->appendChild($IMGURL);
            }
            $photos1 = orm::factory("products_photo")
                    ->where("product_id", "=", $one["id"])
                    ->find_all();

            foreach ($photos1 as $p) {
                if ($p->img) {
                    $IMGURL1 = $xml->createElement('IMAGE', url::base() . "uploads/products/img/" . $p->img);
                    $IMAGES->appendChild($IMGURL1);
                } elseif ($p->imgsrc) {
                    $IMGURL1 = $xml->createElement('IMAGE', $p->imgsrc);
                    $IMAGES->appendChild($IMGURL1);
                }
            }

            $FINAL_PRICE = $xml->createElement('FINAL_PRICE', helper_utils::get_price($one, true));
            $shopitem->appendChild($FINAL_PRICE);

            $ORIGINAL_PRICE = $xml->createElement('ORIGINAL_PRICE', helper_utils::get_price($one, true));
            $shopitem->appendChild($ORIGINAL_PRICE);

            $CURRENCY = $xml->createElement('CURRENCY', "CZK");
            $shopitem->appendChild($CURRENCY);

            $CUSTOMERS = $xml->createElement('CUSTOMERS', $one["orders"]);
            $shopitem->appendChild($CUSTOMERS);

            $dt = date("Y-m-d h:i:s");
            $DEAL_START = $xml->createElement('DEAL_START', $dt);
            $shopitem->appendChild($DEAL_START);

            $dt2 = strtotime($dt);
            $dt2 = strtotime("+3 day", $dt2);
            $dt2 = date("Y-m-d h:i:s", $dt2);
            $DEAL_END = $xml->createElement('DEAL_END', $dt2);
            $shopitem->appendChild($DEAL_END);


            $dt = date("Y-m-d h:i:s");
            $DEAL_START = $xml->createElement('VOUCHER_START', $dt);
            $shopitem->appendChild($DEAL_START);

            $dt2 = strtotime($dt);
            $dt2 = strtotime("+3 day", $dt2);
            $dt2 = date("Y-m-d h:i:s", $dt2);
            $DEAL_END = $xml->createElement('VOUCHER_END', $dt2);
            $shopitem->appendChild($DEAL_END);

            $CATEGORY = $xml->createElement('CATEGORY', $one["esname"]);
            $shopitem->appendChild($CATEGORY);
        }
        //$product->setAttribute("id", "123");
        //$product->appendChild(new DomAttr('id', '123'));
        $xml->save("feeddata4.xml");
        Request::instance()->redirect('/feeddata4.xml');
    }

    function generateEAN($number) {
        $code = '200' . str_pad($number, 9, '0');
        $weightflag = true;
        $sum = 0;
        // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit. 
        // loop backwards to make the loop length-agnostic. The same basic functionality 
        // will work for codes of different lengths.
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int) $code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }

    public function action_product($id) {
        //  $this->auto_render = false;
        $product = orm::factory("product")->where("id", "=", $id)->find();
        if ($product->id) {
            //   if ($product->watch != 1)
            //    Request::instance()->redirect();
            // way and engine configuration
            $papa = $_SESSION["papa"];
            $chek = orm::factory("eshop_menus_product")
                    ->where("eshop_menu_id", "=", $_SESSION["papa"])
                    ->and_where("product_id", "=", $product->id)
                    ->find();
            if ($chek->id) {
                // echo '1';
                $father = orm::factory("eshop_menu")->where("id", "=", $_SESSION["papa"])->find();
            } else {
                //     echo '2';
                $father = $product->eshop_menus->find();
            }
            $way = explode("-", $product->breadcrumb);
            $way1 = $way;
            unset($way1[count($way1) - 1]);
            $way1 = implode("-", $way1);
            $watched = false;
            foreach ($_SESSION["watched"] as $w4)
                if ($w4 == $product->id)
                    $watched = true;
            if (!$watched)
                $_SESSION["watched"][] = $product->id;
            //$objected_way = orm::factory("product")->objected_way('product', $way);
            $objected_way = orm::factory("product")->objected_way('menu', explode("-", $father->breadcrumb));
            $go = true;
            foreach ($objected_way as $one1) {
                if ($one1->watch == 0)
                    $go = false;
            }
            if ($go == false) {
                $bc = explode("-", $product->bc);
                foreach ($bc as $b) {
                    $em = orm::factory("eshop_menu")
                            ->where("index", "=", $b)
                            ->and_where("watch", "=", 1)
                            ->find();
                    if ($em->id)
                        $go = true;
                }
            }
            if (!$go) {
                Request::instance()->redirect(url::base());
            }
            $objected_way[] = $product;
            // echo count($objected_way);
            $comments = $product->get_comments()->as_array();
            $comment_count = count($comments);
            $pagination_set = Kohana::config('pagination.product_comments');
            $pagination_set['total_items'] = $comment_count;

            $this->pagination = new Pagination($pagination_set);

            $product_comments = array_slice($comments, $this->pagination->offset, $this->pagination->items_per_page);

            $pname = $product->name;
            //   $extratitle = "$pname | Обзор, купить $pname в Киеве, Харькове, Днепропетровске, Одессе, Донецке, Запорожье, Львове | www.astromagazin.net - $pname цена, доставка, отзывы, описание, продажа";
//        $page = ORM::factory('menu')->get_menu_by_controller('welcome');

            $data = array(
                'extratitle' => $extratitle,
                'page' => $product,
                'meta' => $product,
                'product' => $product,
                'father' => $father,
                'is_product' => true,
                'watched' => orm::factory("product")->get_watched($product->id),
                'content' => 'product_one.tpl',
                'eshop' => true,
                'way1' => $way1,
                'objected_way' => $objected_way,
                'product_comments' => $product_comments,
                'all_comments' => $comment_count,
                'pagination' => $this->pagination,
                'votes' => $_SESSION['votes'],
                "captcha_cnt" => Captcha::instance()
            );

            unset($_GET['kohana_uri']);
            $this->view->assign($data);
        } else {
            die("Товар существует или удален");
        }
    }

}
