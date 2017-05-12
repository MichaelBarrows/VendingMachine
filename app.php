<?php

// The Vending_Machine object holds all of the applications functionality and data
class Vending_Machine {

    // The __construct method completes a number of tasks on startup of the application, the users balance is set to zero
    // and then the user is asked for the root database password, the database connection for the root user is then
    // declared as a property so there is only one instance of this and the property is referenced throughout the
    // application. The method that sets up the database, user and their privileges is called and then the newly created
    // user is declared as a property as this user will be used throughout the application. Then the rest of the database
    // setup is called through a separate method and the new user is used for this rather than the root user and
    // throughout the application.
    public function __construct() {
        $this->balance = 0;
        $root_pw = $this->db_get_root_pw();
        $this->db_user_root = new PDO('mysql:host=localhost', 'root', $root_pw);
        $this->db_setup_root();
        $this->db_user_vm = new PDO('mysql:host=localhost;dbname=vending_machine', 'vm', 'cw2vm');
        $this->db_setup_vm();
    }

    // Method prints the welcome message and calls the main_menu() method to display the main menu, the message is
    // separated from the main menu to avoid repetition.
    function welcome() {
        print("\e[32mWelcome to the Digital Vending Machine!\e[0m\n\n");
        $this->main_menu();
    }

    // Method prints the main menu, gets input from the user and checks the input is a number by calling the check_user_input()
    // method and checks the result of this comparison, if this is true, the switch statement checks which option has been
    // entered and either destroys the database and its components and exits the application or prints the products menu
    // or the balance menu or the input is not an option within the menu and the user is given an error message, the menu
    // is reprinted and the user is asked to choose a valid option. If the input is not a number, an error message is printed
    // and the user is asked to select another option.
    function main_menu() {
        print("\e[35m## Main Menu ##\e[0m\n");
        print("\e[31m0: Exit \e[0m\n");
        print("\e[92m1: Products \e[0m\n");
        print("\e[92m2: Balance \e[0m\n");
        $menu_option = $this->get_user_input(null, "Choose an option: ", null);
        $check_option = $this->check_user_input($menu_option);
        if ($check_option) {
            switch($menu_option) {
                case 0:
                    $this->db_destroy();
                    exit();
                    break;
                case 1:
                    return $this->products_menu();
                    break;
                case 2:
                    return $this->balance_menu();
                    break;
                default:
                    $this->invalid_choice("option");
                    return $this->main_menu();
                    break;
            }
        } else {
            $this->invalid_choice("num");
            $this->main_menu();
        }
        return null;
    }

    // Method takes two parameters, $text is text that is printed a line before the user is asked to input their option
    // and the $prefix is printed on the same line as the user inputs their option. The $text parameter is checked to see
    // if it is not empty and prints the text is it exists. the user is then asked for their input with the $prefix on the
    // same line and the input is then returned back to the function that requested the input.
    function get_user_input($text, $prefix, $type) {
        print("\e[33m");
        if ($text) {
            print($text . "\n");
        }
        if ($type == "pw") {
            print("\e[30m");
        } else {

        }
        $user_input = readline($prefix);
        print("\e[0m\n");
        return $user_input;
    }

    // This method gets one parameter from the method that has called it, the $input is the users input that is to be
    // checked. This method is able to check if the input is numeric, the function is_int() is not used as all user
    // input is a string
    function check_user_input($input) {
        if (is_numeric($input)) {
            return true;
        } else {
            return false;
        }
    }

    // This method is called to print error messages, when the user enters the wrong type of data or selects an option that
    // was not presented to them, this method is able to print error messages for a variety of input requirements, including
    // number, products, yes/no and menu options.
    function invalid_choice($type) {
        // Prints an error message where numeric data is required.
        if ($type == "num") {
            print("\e[31m** Invalid selection! Please enter a number **\e[0m\n\n");
        // Prints an error message where the selected product does not exist.
        } else if ($type == "prod") {
            print("\e[31m** Invalid selection! Please choose a product from the list **\e[0m\n\n");
        // Prints an error message where yes(y)/no(n) input is required.
        } else if ($type == "y/n") {
            print("\e[31m** Invalid selection! Please choose Y or N **\e[0m\n\n");
        // Prints an error message where the selected menu option is not available.
        } else if ($type == "option") {
            print("\e[31m** Invalid selection! Please choose an option from the menu **\e[0m\n\n");
        }
    }

    // Displays the balance menu to the user and allows them to select an option by calling the get_user_input() method,
    // the input is then checked to ensure it is of the correct type for the balance menu using check_user_input() method 
    // the users input ($menu_option) is then checked using a switch statement to determine which part of the application
    // to go to next. if the user selects an option that is not valid, they are given an error message and the menu is 
    // displayed again.
    public function balance_menu() {
        print("\e[35m## Balance Menu ##\e[0m\n");
        print("\e[92m0: Main Menu \e[0m\n");
        print("\e[92m1: View Balance \e[0m\n");
        print("\e[92m2: Add Credit \e[0m\n");
        print("\e[92m3: Refund Credit \e[0m\n");
        print("\e[92m4: View Transactions \e[0m\n");
        $menu_option = $this->get_user_input(null, "Choose an option: ", null);
        // Checks if the input is a number.
        $check_option = $this->check_user_input($menu_option);
        // Confirms if the users input is a number.
        if ($check_option) {
            // Uses the user input to move to the appropriate functionality.
            switch($menu_option) {
                // Calls the main_menu() method which displays the main menu.
                case 0:
                    return $this->main_menu();
                    break;
                // Calls the view_balance() method which displays the users balance.
                case 1:
                    return $this->view_balance();
                    break;
                // Calls the add_credit() method which allows the user to add to their balance.
                case 2:
                    return $this->add_credit();
                    break;
                // Calls the refund() method which "gives" the user their money back.
                case 3:
                    return $this->refund();
                    break;
                // Calls the view_transactions() method which gets the users transactions from the transactions db table
                // and displays them as a list to the user.
                case 4:
                    return $this->view_transactions();
                    break;
                // Displays an error message is the selected option does not exist and displays the balance menu again.
                default:
                    $this->invalid_choice("option");
                    return $this->balance_menu();
                    break;
            }
        // The users choice is not a number, so an error message is displayed and the balance menu is displayed again.
        } else {
            $this->invalid_choice("num");
            $this->balance_menu();
        }
        return null;
    }

    // This method allows the user to add credit to their balance, it requests a numeric input from the user and checks
    // the input is a number, if this is true, the amount is added to the balance, the user is notified of their new
    // balance and the transaction is added to the transactions db table, finally the balance menu is displayed again so
    // the user can navigate through the app. If the user does not enter a number, an error message is displayed and the
    // user is asked to choose an option from the menu as it is reprinted. With the users updated balance, they can then
    // purchase products.
    public function add_credit() {
        $amount = $this->get_user_input("How much credit would you like to add?", "£", null);
        $amount_check = $this->check_user_input($amount);
        if ($amount_check) {
            $this->balance += $amount;
            print("\e[32mYour new balance is £" . $this->balance . "\e[0m\n\n");
            $this->db_add_transaction("a", "Balance", $amount);
            return $this->balance_menu();
        } else {
            $this->invalid_choice("num");
            return $this->balance_menu();
        }
    }

    // Function to get the users balance, without printing it to the user.
    function system_view_balance() {
        return $this->balance;
    }

    // Displays the users current balance and then displays the balance menu to allow the user to navigate to another
    // part of the application. The balance gives the user an indication as to whether they can afford a product or not.
    function view_balance() {
        print("\e[32mYour current balance is: £" . $this->balance . "\e[0m\n\n");
        return $this->balance_menu();
    }

    // Tells the user their current balance has been refunded, adds the transaction to the transactions table and sets
    // the users balance to zero, simulating a refund and recording the transaction. The balance menu is then displayed
    // to allow navigation to other parts of the app.
    function refund() {
        print("\e[32mYou have been refunded £" . $this->balance . "\e[0m\n\n");
        $this->db_add_transaction("s", "Refund", $this->balance);
        $this->balance = 0;
        return $this->balance_menu();
    }

    // This method takes one parameter ($amount), this method is called when a user makes a purchase and is used to reduce
    // their balance by the amount of the product. the parameter $amount contains a numeric value to reduce the users balance
    // by, the new balance is then returned to the method that called this one.
    function reduce_balance($amount) {
        $this->balance -= $amount;
        return $this->balance;
    }

    // This method displays a list of transactions to the user, this works by calling the db_view_transactions() method,
    // which will print the list of the users transactions from the database table transactions, since the application was
    // started. Finally the users current balance is printed and the balance menu to allow the user to navigate around the
    // application
    function view_transactions() {
        print("\e[35mYour transactions\e[0m\n");
        $this->db_view_transactions();
        $this->view_balance();
        return $this->balance_menu();
    }

    // Displays the products menu to the user and allows them to select an option by calling the get_user_input() method,
    // the input is then checked to ensure it is of the correct type for the balance menu using check_user_input() method
    // the users input ($menu_option) is then checked using a switch statement to determine which part of the application
    // to go to next, by calling the relevant method. if the user selects an option that is not valid, they are given an
    // error message and the menu is displayed again.
    function products_menu() {
        print("\e[35m## Products Menu ##\e[0m\n");
        print("\e[92m0: Main Menu \e[0m\n");
        print("\e[92m1: View Products \e[0m\n");
        print("\e[92m2: Buy Product \e[0m\n");
        $menu_option = $this->get_user_input(null, "Choose an option: ", null);
        $check_option = $this->check_user_input($menu_option);
        if ($check_option) {
            switch($menu_option) {
                case 0:
                    return $this->main_menu();
                    break;
                case 1:
                    return $this->view_products();
                    break;
                case 2:
                    return $this->buy_product();
                    break;
                default:
                    $this->invalid_choice("option");
                    return $this->products_menu();
                    break;
            }
        } else {
            $this->invalid_choice("num");
            $this->products_menu();
        }
        return null;
    }

    // This method calls a method that gets the products from the databases products table and prints them for the user,
    // the products menu is displayed afterwards so the user does not get stuck within the application.
    function view_products() {
        $this->db_view_products();
        return $this->products_menu();
    }

    // This method gets an input from the user which should be a numeric product code, the users input is checked to see
    // if it is a numeric value, the value entered by the user is then passed to the db_lookup_product method which will
    // return an array which holds the name and price of the product, the variable ($product_lookup) is then checked to
    // see if it has any value. If the $product_lookup variable has a value, a line is printed that ask the user to
    // confirm this is the product they want to purchase, they are asked to enter Y or N to indicate their willingness
    // to continue with the purchase. If they enter Y to indicate they are, the users balance - the product amount are
    // checked to see if the purchase occurred, their balance would not go below zero. If this is true, the purchase
    // continues and the users balance is reduced by the products amount and they are told their transaction was successful
    // and their new balance is displayed. The transaction is then added to the transaction table of the database and the
    // purchase is complete. If the user does not have sufficient funds to make the purchase, an error message is displayed.
    // If the user has decided not to proceed with the purchase, a message to this effect is printed. If the user did not
    // enter Y or N, an error message is printed as their input is not valid. If the product does not exist, an error
    // message is displayed and this method is called again. If the user did not enter a number for the product code,
    // their input is not valid, an error message is printed and this method is called again. Finally the products menu
    // is displayed again.
    function buy_product() {
        $product_id = $this->get_user_input("Please enter the product code of the product you would like to buy \n\e[31mEnter 0 to go back\e[33m", "Product Code: ", null);
        // Check the user has entered a number
        if ($this->check_user_input($product_id)) {
            $product_lookup = $this->db_lookup_product($product_id);
            if ($product_lookup == 0) {
                return $this->products_menu();
            // Checks if the product exists in the products table of the database
            } else if (isset($product_lookup)) {
                $buy_message = "You are buying: " . $product_lookup['name'] . " for £" . $product_lookup['price'] . "?";
                // Converts the users input to lower case.
                $confirm = strtolower($this->get_user_input($buy_message, "Y/N: ", null));
                if ($confirm == "y") {
                    // Checks the user has enough money to purchase the product
                    if ($this->system_view_balance() - $product_lookup['price'] >= 0) {
                        $new_balance = $this->reduce_balance($product_lookup['price']);
                        print("\e[32mYour purchase of " . $product_lookup['name'] . " was successful.\e[0m\n");
                        print("\e[32mYour new balance is £" . $new_balance . ".\e[0m\n\n");
                        $this->db_add_transaction("s", $product_lookup['name'], $product_lookup['price']);
                    // The user does not have enough money to make the purchase
                    } else {
                        print("\e[31m** You have insufficient funds to make this purchase **\e[0m\n");
                        print("\e[31m** Please add credit to continue **\e[0m\n\n");
                        $this->view_balance();
                    }
                // The user has cancelled the purchase
                } else if ($confirm == "n") {
                    print("\e[31m** Your purchase has been cancelled. You have not been charged **\e[0m\n\n");
                } else {
                    $this->invalid_choice("y/n");
                    $this->buy_product();
                }
            // The product ID entered by the user is not valid
            } else {
                $this->invalid_choice("prod");
                $this->buy_product();
            }
        } else {
            $this->invalid_choice("num");
            $this->buy_product();
        }
        return $this->products_menu();
    }

    // This method calls three other methods to create the database user, the database and grant the user privileges on
    // the database. Uses the root user.
    function db_setup_root() {
        $this->db_create_user();
        $this->db_create_database();
        $this->db_grant_privileges();
    }

    // Get the password for the root database user from the user, which is then used to create the database, database user
    // and grant privileges to the new user. When the application is terminated from the main menu, the root user password
    // is used to delete the database and the user.
    function db_get_root_pw() {
        $root_pw = $this->get_user_input('Please enter the root password: ', null, 'pw');
        return $root_pw;
    }

    // This method calls three other methods to create the tables in the database and add products to the products table.
    // Uses the vm user.
    function db_setup_vm() {
        $this->db_create_products_table();
        $this->db_create_trans_table();
        $this->db_add_products();
    }

    // This method creates a separate user to handle database queries, so that the root user is not used for transferring
    // data from the database.
    function db_create_user() {
        $statement = $this->db_user_root->prepare('CREATE USER "vm"@"localhost" IDENTIFIED BY "cw2vm";');
        $statement->execute();
    }

    // This method creates the database that holds the data, the root user is used because then newly created user does
    // not have the necessary privileges to complete this task.
    function db_create_database() {
        $statement = $this->db_user_root->prepare('CREATE DATABASE vending_machine;');
        $statement->execute();
    }

    // This method is used to give the newly created user the necessary permissions to complete the required tasks on
    // the database.
    function db_grant_privileges() {
        $statement = $this->db_user_root->prepare('GRANT ALL ON vending_machine.* TO "vm"@"localhost";');
        $statement->execute();
    }

    // This method creates the products table within the database that will hold the products, this table will hold an
    // identifier, the products name and the products price.
    function db_create_products_table() {
        $statement = $this->db_user_vm->prepare('CREATE TABLE products(
          id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(30) NOT NULL,
          price FLOAT NOT NULL
          );
        ');
        $statement->execute();
    }

    // This method creates the transactions table within the database that will hold the users transactions, this table
    // will hold an identifier for the transaction, the type of transaction (a or s) to imply whether the value was added
    // or subtracted from the balance/ the product will either be credit, refund or the name of the product purchased.
    // The amount is a number that was involved in the transaction.
    function db_create_trans_table() {
        $statement = $this->db_user_vm->prepare('CREATE TABLE transactions(
          id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
          type VARCHAR(1) NOT NULL,
          product VARCHAR(10),
          amount FLOAT NOT NULL
          );
        ');
        $statement->execute();
    }

    // This method adds products to the products table of the database for the user to purchase.
    function db_add_products() {
        $statement = $this->db_user_vm->prepare('INSERT INTO products(name, price) VALUES(:name, :price)');
        $statement->execute(array('name' => 'KitKat', 'price' => '0.65'));
        $statement->execute(array('name' => 'Milky Way', 'price' => '0.65'));
        $statement->execute(array('name' => 'Mars', 'price' => '0.65'));
        $statement->execute(array('name' => 'Snickers', 'price' => '0.65'));
        $statement->execute(array('name' => 'Fanta', 'price' => '0.95'));
        $statement->execute(array('name' => 'Coca Cola', 'price' => '0.95'));
    }

    // This method adds transactions to the transactions table of the database when money is added or subtracted from the
    // users balance.
    function db_add_transaction($type, $product, $amount) {
        $statement = $this->db_user_vm->prepare('INSERT INTO transactions(type, product, amount) VALUES(:type, :product, :amount)');
        $statement->execute(array('type' => $type, 'product' => $product, 'amount' => $amount));
    }

    // This method retrieves a list of products from the products table within the database, and prints the list of
    // products available for purchase. the ID, name and price for each product is printed for the user to choose from.
    function db_view_products() {
        print("\e[35mProducts\e[0m\n");
        $statement = $this->db_user_vm->prepare('SELECT * FROM products');
        $statement->execute();
        $result = $statement->fetchAll();
        for ($idx = 0; $idx < count($result); $idx++) {
            print("\e[94m" . $result[$idx]['id'] . ":\e[93m " . $result[$idx]['name'] . " \e[92m£" . $result[$idx]['price'] . "\n");
        }
        print("\n");
    }

    // This method gets a list of the users transactions from the transactions database and prints each transaction for
    // the user to view, a '+' or '-' is prefixed to the amount to indicate if the amount is a deposit or withdrawal/
    // expenditure. The transactions ID, product, type and amount is printed for the user.
    function db_view_transactions() {
        $statement = $this->db_user_vm->prepare('SELECT * FROM transactions');
        $statement->execute();
        $result = $statement->fetchAll();
        if (count($result) > 0) {
            for ($idx = 0; $idx < count($result); $idx++) {
                if ($result[$idx]['type'] == "s") {
                    $type = "\e[31m-";
                } else {
                    $type = "\e[32m+";
                }
                print($result[$idx]['id'] . ": " . $result[$idx]['product'] . " " . $type . "£" . $result[$idx]['amount'] . "\e[0m\n");
            }
        } else {
            print("\n\e[31m** There are no transactions to display **\e[1m\n");
        }
        print("\n");
    }

    // This method has one parameter which is a numeric value representing a product ID, this is used to only select this
    // product from the products table of the database, and returns the result of this to the caller method. if the
    // product is not found, null is returned.
    function db_lookup_product($product_id){
        if ($product_id == 0) {
            return 0;
        }
        $statement = $this->db_user_vm->prepare('SELECT name, price FROM products WHERE id = :id');
        $statement->execute(array('id' => $product_id));
        $result = $statement->fetchAll();
        if (isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }

    // When the program is terminated from the main menu, this method is called to call various methods that will remove
    // the databases tables, the database itself and the user created to interact with the database.
    function db_destroy() {
        $this->db_drop_products_table();
        $this->db_drop_trans_table();
        $this->db_drop_database();
        $this->db_drop_user_vm();
    }

    // This method removes the database table that holds the products, so that if the application is loaded on the same
    // computer, the table will only contain once instance of each product, rather than repeatedly adding products to
    // the table when the app is run.
    function db_drop_products_table(){
        $statement = $this->db_user_vm->prepare('DROP TABLE products');
        $statement->execute();
    }

    // This method removed the database table that holds the transactions, so the transactions are not carried over to
    // the next session
    function db_drop_trans_table(){
        $statement = $this->db_user_vm->prepare('DROP TABLE transactions');
        $statement->execute();
    }

    // This method removes the database so that no data is left behind for the next users session, assuming this product
    // is ran repeatedly on the same computer.
    function db_drop_database(){
        $statement = $this->db_user_root->prepare('DROP DATABASE vending_machine');
        $statement->execute();
    }

    // This method removes the database user so that it is not left behind on the users computer.
    function db_drop_user_vm(){
        $statement = $this->db_user_root->prepare('DROP USER "vm"@"localhost";');
        $statement->execute();
    }
}

// Creates the Vending_Machine object, ready for the application to begin.
$vending_machine = new Vending_Machine();

// Calls the welcome method to start the application, from here each method calls another method so the user does not
// end up stuck within the application.
$vending_machine->welcome();