<?php
/**
 * @plugin ScanBookPlugin
 * 
 * Plugin Name: Scan Books
 * Plugin URI: 
 * Description: Scan your books for a bookdatabase
 * Version: 1.0
 * Author: Janina Ortelt
 * Author URI: 
 * License: free
*/
 // Deny unauthorized access
defined('ABSPATH')or die('Access denied'); 
// check if a class with the name "ScanBooks" already exist
if ( ! class_exists('ScanBooks')) {
    // if the class doesn't exists create the class
    class ScanBooks {
        // Method: activate()
        // what should happen by activating the plugin
        function activate() {}
        // Method: deactivate()
        // What should happen by deactivating the plugin
        function deactivate() {}
        // Method: create_post_types()
        // hook custom methods
        function add_customs() {
            // add a menu entry to admin menu by hooking the bookmenu() method
            add_action('admin_menu', array($this, 'bookmenu')); 
            // add head entry to admin page (custom css/javascript) by hooking admin_register_head() method
            add_action('admin_head', array($this,'admin_register_head'));
        }
        // Method: bookmenu()
        // create custom admin menu entry
        function bookmenu() {
            add_menu_page('Books', 'Books', 10, __FILE__, array($this, 'books'), 'dashicons-book', 4); 
            add_submenu_page(__FILE__, 'Book Series', 'Book Series', 10, 'bookseries', array($this, 'bookseries')); 
            add_submenu_page(__FILE__, 'Book List', 'Book List', 10, 'booklist', array($this, 'booklist')); 
            add_submenu_page(__FILE__, 'Scan Book', 'Scan Book', 10, 'scanbook', array($this, 'scanbook')); 
            add_submenu_page(__FILE__, 'Add manually', 'Add manually', 10, 'addmanually', array($this, 'addmanually')); 
        }
        // Method: admin_register_head()
        // create custom admin site head        
        function admin_register_head() {
            $siteurl = get_option('siteurl');
            $style  = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/assets/main.css';
            $script = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/assets/script.js';
            echo '<link rel="stylesheet" type="text/css" href="'.$style.'"/>';
            echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>';
            echo '<script type="text/javascript" src="'.$script.'"></script>';
        }
        // Method: books()
        // Show some statistics
        function books() {}
        // Method: booklist()
        // List all books
        function booklist() {
            // insert view (call method: show_books())
            $this->show_books();
        }
        // Method: bookseries()
        // List alls book series
        function bookseries() {
            // insert view  (call method: show_book_series())
            $this->show_book_series();
        }
        // Method: scanbook()
        // code to scan books
        function scanbook() {}
        // Method: addmanually()
        // Site for munally adding books
        function addmanually() {
            // insert view (call method: add_book_form())
            $this->add_book_form();
            // handling after submit "book serie"
            if(isset($_POST['book_add_series'])){
                // use wordpress database
                global $wpdb;
                $error = 0;
                // check if inputs are empty
                if(empty($_POST['book_series_title'])){
                    echo '<p style="color:red">You have to insert a Book Title</p>';
                    $error++;
                }
                if(empty($_POST['book_series_autor'])){
                    echo '<p style="color:red">You have to insert an autor name</p>';
                    $error++;
                }
                if(empty($_POST['book_series_volumes'])){
                    echo '<p style="color:red">Please insert number of volumes</p>';
                    $error++;
                }
                // sanitize inputs
                $book_series_title   = sanitize_text_field( $_POST['book_series_title'] );  update_post_meta( $post->ID, 'book_series_title', $book_series_title );
                $book_series_autor   = sanitize_text_field( $_POST['book_series_autor'] );  update_post_meta( $post->ID, 'book_series_autor', $book_series_autor );
                $book_series_volumes = intval( $_POST['book_series_volumes'] );             update_post_meta( $post->ID, 'book_series_volumes', $book_series_volumes );
                // get book_series entry by posted book_series_title, to check, if this serie already exits in Database
                $book_series_id = $wpdb->get_row("SELECT book_series_id FROM wp_book_series WHERE book_series_title = '$book_series_title'");  
                // if no error and the book serie doesn't already exist in the Darabase
                if($error == 0 && empty($book_series_id)) {
                    // add new book serie to database
                    $sql = $wpdb->prepare("INSERT INTO wp_book_series (`book_series_title`, `book_series_autor`, `book_series_volumes`) 
                                           VALUES (%s, %s, %d)", $book_series_title, $book_series_autor, $book_series_volumes);
                    $wpdb->query($sql);
                    // if successful, send success message
                    echo '<p style="color:green">Added book series successfully</p>';
                    // call method redirect() with string parameter 
                    $this->redirect("../wp-admin/admin.php?page=addmanually");
                } else {
                    // if a book serie with this name already exist send "error" message
                    echo "This book series already exists.<br>";
                } 
            }    
            // handling after submit "book"
            if(isset($_POST['book_add'])){
                // use wordpress database
                global $wpdb;
                // sanitize inputs
                $book_series_id     = intval( $_POST['book_series_id'] );                 update_post_meta( $post->ID, 'book_series_id', $book_series_id );
                $book_publisher_id  = intval( $_POST['book_publisher_id'] );              update_post_meta( $post->ID, 'book_publisher_id', $book_publisher_id );
                $book_volume        = intval( $_POST['book_volume'] );                    update_post_meta( $post->ID, 'book_volume', $book_volume );
                $book_published     = intval( $_POST['book_published'] );                 update_post_meta( $post->ID, 'book_publisher_id', $book_published );
                $book_synopsis      = sanitize_textarea_field( $_POST['book_synopsis'] ); update_post_meta( $post->ID, 'book_synopsis', $book_synopsis );
                // get book entry by book_volume and book_series_id, to check if this volume of this book series already exists
                $check_book = $wpdb->get_row("SELECT * FROM wp_books b 
                                              JOIN wp_book_series s 
                                              ON s.book_series_id = b.book_series_id 
                                              WHERE book_volume = '$book_volume'
                                              AND b.book_series_id = '$book_series_id'");
                // if this book doesn't exist
                if(empty($check_book)) {
                    // add new book to database
                    $sql = $wpdb->prepare("INSERT INTO wp_books (`book_series_id`, `book_publisher_id`, `book_volume`, `book_published`, `book_synopsis`) 
                                           VALUES (%d, %d, %d, %s, %s)", $book_series_id, $book_publisher_id, $book_volume, $book_published, $book_synopsis);
                    $wpdb->query($sql);
                    // if successful, send success message
                    echo '<p style="color:green">Added your book successfully</p>';
                    // call method redirect() with string parameter 
                    $this->redirect("../wp-admin/admin.php?page=addmanually");
                } else {
                    // if a book with this name already exist send "error" message
                    echo "This book already exists.";
                }
            }
        }
        // Method: redirect()
        // Parameter: $url
        // this will add a short javascript for redirection
        function redirect($url){
            $string = '<script type="text/javascript">';
            $string .= 'function Redirect(){';
            $string .= 'window.location = "' . $url . '"';
            $string .= '}';
            $string .= 'setTimeout("Redirect()", 2000);';
            $string .= '</script>';
            echo $string;
        }
        // #############  Views  ##############################
        // Method: show_book_series()
        // Html for the book series view
        function show_book_series(){
            // use wordpress database
            global $wpdb;
            // collect Book series data from database
            $series = $wpdb->get_results("SELECT * FROM wp_book_series");
            // Set HTML
            echo    '<div class="wrap">';
            echo    '   <h1 class="wp-heading-inline">Book Series</h1>';
            echo    '   <hr class="wp-header-end">';
            echo    '   <br>';
            echo    '       <div class="list">';
            echo    '           <div class="list_head">';
            echo    '               <div class="titles">ID</div>';
            echo    '               <div class="titles">Title</div>';
            echo    '               <div class="titles">Autor</div>';
            echo    '               <div class="titles">Volumes</div>';
            echo    '           </div>';
            // Publish Book Data
            // check if there are book series in Database
                            if(!empty($series)) {
            // if entries found show data 
                                foreach($series as $serie){
            echo    '               <div class="rows">';                                    
            echo    '                   <div class="row">'.$serie->book_series_id.'</div>';
            echo    '                   <div class="row">'.$serie->book_series_title.'</div>';
            echo    '                   <div class="row">'.$serie->book_series_autor.'</div>';
            echo    '                   <div class="row">'.$serie->book_series_volumes.'</div>';
            echo    '               </div>'; 
                                }
                            } else {
            // if no entries found show message 
            echo    '           <div class="empty">No entries found</div>';
                            }
            echo    '           <div class="list_head">';
            echo    '               <div class="titles">ID</div>';
            echo    '               <div class="titles">Title</div>';
            echo    '               <div class="titles">Autor</div>';
            echo    '           <div class="titles">Volumes</div>';
            echo    '       </div>';
            echo    '   </div>';    
            echo    '</div>';
        }
        // Method: show_books()
        // Html for the books view
        function show_books(){
            // use wordpress database
            global $wpdb;
            // collect Book data from database
            $books = $wpdb->get_results("SELECT * FROM wp_books");
            // Set HTML
            echo    '<div class="wrap">';
            echo    '   <h1 class="wp-heading-inline">Book List</h1>';
            echo    '   <hr class="wp-header-end">';
            echo    '   <br>';
            echo    '   <div class="list_books">';
            echo    '       <div class="list_head_books">';
            echo    '           <div class="titles_books">ID</div>';
            echo    '           <div class="titles_books">Title</div>';
            echo    '           <div class="titles_books">Autor</div>';
            echo    '           <div class="titles_books">Volume</div>';
            echo    '           <div class="titles_books">Publisher</div>';
            echo    '           <div class="titles_books">Year</div>';
            echo    '       </div>';
            // Publish Book Data
            // check if there are books in Database
                        if(!empty($books)) {
            // if entries found show data                 
                            foreach($books as $book){
                                // get book series data by book_series_id
                                $series     = $wpdb->get_row("SELECT * FROM wp_book_series WHERE book_series_id = '$book->book_series_id'");
                                // get publisher data by book_series_id
                                $publisher  = $wpdb->get_row("SELECT * FROM wp_book_publishers WHERE book_publisher_id = '$book->book_publisher_id'");
            echo    '       <div class="rows">';                                   
            echo    '           <div class="row_books">'.$book->book_id.'</div>';
            echo    '           <div class="row_books">'.$series->book_series_title.'</div>';
            echo    '           <div class="row_books">'.$series->book_series_autor.'</div>';
            echo    '           <div class="row_books">'.$book->book_volume.'</div>';
            echo    '           <div class="row_books">'.$publisher->book_publisher_name.'</div>';
            echo    '           <div class="row_books">'.$book->book_published.'</div>';
            echo    '       </div>'; 
                            }
             // if no entries found show message 
                        } else {
            echo    '       <div class="empty">No entries found</div>';
                        }
            echo    '       <div class="list_head_books">';
            echo    '           <div class="titles_books">ID</div>';
            echo    '           <div class="titles_books">Title</div>';
            echo    '           <div class="titles_books">Autor</div>';
            echo    '           <div class="titles_books">Volume</div>';
            echo    '           <div class="titles_books">Publisher</div>';
            echo    '           <div class="titles_books">Year</div>';
            echo    '       </div>';
            echo    '   </div>';    
            echo    '</div>';
        }
        // Method: add_book_form()
        // Html for adding books and series
        function add_book_form() {
            // use wordpress database
            global $wpdb;
            // collect book series data from database
            $series     = $wpdb->get_results("SELECT * FROM wp_book_series");
            // collect publisher data from database
            $publishers = $wpdb->get_results("SELECT * FROM wp_book_publishers");
            // if a book serie is selected, it will post the book_series_id
            if(isset($_POST['book_series_id'])){
                // sanitize input
                $book_series_id = intval( $_POST['book_series_id'] ); update_post_meta( $post->ID, 'book_series_id', $book_series_id );
                // get number of volumes which belong to the book serie  
                $volumes = $wpdb->get_row("SELECT * FROM wp_book_series WHERE book_series_id = '$book_series_id'");
            }
            echo    '<div class="wrap">';
            echo    '   <h1 class="wp-heading-inline">Add Book Manually</h1>';
            echo    '   <hr class="wp-header-end">';
            echo    '   <br>';
            echo    '   <div class="form">';
            echo    '       <form method="POST" id="add_books" name="add_books">';
            echo    '           <label for="new_series">New Book Series?</label><br>';
            echo    '           <input type="radio" name="new_series" id="new_series" value="no" checked>No';
            echo    '           <input type="radio" name="new_series" id="new_series" value="yes">Yes<br>';
            echo    '           <div id="newSeries">';
            echo    '               <label for="book_series_title">Choose Title</label><br>';
            echo    '               <form method="POST" id="bookTitle" name="bookTitle">';
            echo    '               <select name="book_series_id" id="book_series_id" onchange="this.form.submit()">';
            echo    '                   <option>Choose a Book Series</option>';
            // check if book series are emtpty
                                    if(!empty($series)){
            // if book series are not empty show data
                                        foreach ($series as $series) {
                                            if($series->book_series_id == $selectedID){
            echo    '                           <option value="'.$series->book_series_id.'" selected="selected">'.$series->book_series_title.'</option>';
                                            } else {
            echo    '                           <option value="'.$series->book_series_id.'">'.$series->book_series_title.'</option>';
                                            }
                                        }
                                    } else {
            echo    '                       <option>No Series</option>';                            
                                    }
            echo    '               </select></form><br>';
            echo    '               <label for="book_volume">Choose Volume</label><br>';
            echo    '               <select name="book_volume" id="book_volume">';
            // loop numbers from 1 to total numbers of volumes for the selected book series
                                    for($vol = 1; $vol <= $volumes->book_series_volumes; $vol++){
            echo    '                   <option value="'.$vol.'">'.$vol.'</option>';
                                    }
            echo    '               </select><br>';
            echo    '               <label for="book_publisher_id">Choose Publisher</label><br>';
            echo    '               <select name="book_publisher_id" id="book_publisher_id">';
            // show publisher names
                                    foreach ($publishers as $publisher) {
            echo    '                   <option value="'.$publisher->book_publisher_id.'">'.$publisher->book_publisher_name.'</option>';
                                    }
            echo    '               </select><br>';
            echo    '               <label for="book_published">Year of publication</label><br>';
            echo    '                <select name="book_published" id="book_published">';
            // loop year from 1900 until today
                                    for($year = 1900; $year <= date("Y"); $year++){
            echo    '                   <option value="'.$year.'">'.$year.'</option>';
                                    }
            echo    '               </select><br>';
            echo    '               <label for="book_synopsis">Book Synopsis</label><br>';
            echo    '               <textarea rows="10" cols="43" name="book_synopsis" id="book_synopsis"></textarea>';
            echo    '               <input type="submit" name="book_add" value="Add new Book" id="createusersub" class="button button-primary"/>';
            echo    '           </div>';
            echo    '       </form>';
            echo    '       <form method="POST" id="add_series" name="add_series">';    
            echo    '           <div id="notNew" style="display: none">';
            echo    '               <label for="book_series_title">Title</label><br>';
            echo    '               <input type="text" name="book_series_title" id="book_series_title" value="" size="40" />';
            echo    '               <label for="book_series_autor">Autor</label><br>';
            echo    '               <input type="text" name="book_series_autor" id="book_series_autor" value="" size="40" />';
            echo    '               <label for="book_series_volumes">Total Volumes:</label><br>';
            echo    '               <input type="text" name="book_series_volumes" id="book_series_volumes" value="" size="5" /><br>';
            echo    '               <input type="submit" name="book_add_series" value="Add new Book Series" id="createusersub" class="button button-primary"/>';
            echo    '           </div>';
            echo    '       </form>';    
            echo    '   </div>';
            echo    '</div>';
        }
    }
}
// create new Object of the class ScanBook
$scanBooks = new ScanBooks();
// call Method add_post_types()
$scanBooks->add_customs(); 
// to register the activation hook 
// register_activation_hook(__FILE__, array($scanBooks, 'activate')); 
// to register the deactivation hook 
// register_deactivation_hook(__FILE__, array($scanBooks, 'deactivate'));
