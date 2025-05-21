<?php

class ContactController{
    public static function contact(){
        require_once 'view/contactView.php';
    }

    public static function send(){
        if(ContactController::checkAttributs()){
            $to = "hugo.darthenay@gmail.com";
            $subject = "Message de Visiroom";
            $message = "Nom : ".$_POST["nom"]."\nEmail : ".$_POST["email"]."\nMessage : ".$_POST["message"];
            $header = "Content-type: text/plain; charset=utf-8\r\nFrom: mail-out.cluster027.hosting.ovh.net\r\n";

            mail($to, $subject, $message, $header);
        }

        require_once 'view/contactView.php';
    }

    public static function checkAttributs(){
        $erreur = array('nom' => '', 'email' => '', 'message' => '');
        $success = true;

        //Nom
        if(isset($_POST["nom"]) && !empty($_POST["nom"])){
            if(strlen($_POST["nom"]) > 200){
                $success = false;
                $erreur["nom"] = "200 caractères maximum";
            }
        }else{
            $success = false;
            $erreur["nom"] = "Champ obligatoire";
        }

        //Email
        if(isset($_POST["email"]) && !empty($_POST["email"])){
            if(strlen($_POST["email"]) > 300){
                $success = false;
                $erreur["email"] = "300 caractères maximum";
            }
        }else{
            $success = false;
            $erreur["email"] = "Champ obligatoire";
        }

        //Message
        if(isset($_POST["message"]) && !empty($_POST["message"])){
            if(strlen($_POST["message"]) > 1000){
                $success = false;
                $erreur["message"] = "1000 caractères maximum";
            }
        }else{
            $success = false;
            $erreur["message"] = "Champ obligatoire";
        }

        return $success;
    }
}