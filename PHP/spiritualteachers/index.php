<?php

/**
 * Created by Oclemy for http://camposha.info and ProgrammingWizards TV.
 * User: Oclemy
 */
class Constants
{
    //DATABASE DETAILS
    static $DB_SERVER="localhost";
    static $DB_NAME="spiritualTeachersDB";
    static $USERNAME="sisi";
    static $PASSWORD="pass";

    //STATEMENTS
    static $SQL_SELECT_ALL="SELECT * FROM spiritualTeachersTB";
}

class Spirituality
{
    /*******************************************************************************************************************************************/
    /*
       1.CONNECT TO DATABASE.
       2. RETURN CONNECTION OBJECT
    */
    public function connect()
    {
        $con=new mysqli(Constants::$DB_SERVER,Constants::$USERNAME,Constants::$PASSWORD,Constants::$DB_NAME);
        if($con->connect_error)
        {
           // echo "Unable To Connect";
            return null;
        }else
        {
            return $con;
        }
    }
    public function insert()
    {
        // INSERT
        $con=$this->connect();
        if($con != null)
        {
            // Get image name
            $image_name = $_FILES['image']['name'];
            // Get text
            $teacher_name = mysqli_real_escape_string($con, $_POST['teacher_name']);
            $teacher_description = mysqli_real_escape_string($con, $_POST['teacher_description']);

            // image file directory
            $target = "images/".basename($image_name);
            $sql = "INSERT INTO spiritualTeachersTB (teacher_image_url, teacher_name,teacher_description) VALUES ('$image_name', '$teacher_name', '$teacher_description')";
            try
            {
                $result=$con->query($sql);
                if($result)
                {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                       print(json_encode(array("message"=>"Success")));
                    }else{
                       print(json_encode(array("message"=>"Saved But Unable to Move Image to Appropriate Folder")));
                    }
                }else
                {
                    print(json_encode(array("message"=>"Unsuccessful. Connection was successful but data could not be Inserted.")));
                }
                $con->close();
            }catch (Exception $e)
            {
                print(json_encode(array("message"=>"ERROR PHP EXCEPTION : CAN'T SAVE TO MYSQL. ".$e->getMessage())));
                $con->close();
            }
        }else{
            print(json_encode(array("message"=>"ERROR PHP EXCEPTION : CAN'T CONNECT TO MYSQL. NULL CONNECTION.")));
        }
    }
    /*******************************************************************************************************************************************/
    /*
       1.SELECT FROM DATABASE.
    */
    public function select()
    {
        $con=$this->connect();
        if($con != null)
        {
            $result=$con->query(Constants::$SQL_SELECT_ALL);
            if($result->num_rows > 0)
            {
                $spiritual_teachers=array();
                while($row=$result->fetch_array())
                {
                    array_push($spiritual_teachers, array("id"=>$row['id'],"teacher_name"=>$row['teacher_name'],"teacher_description"=>$row['teacher_description'],"teacher_image_url"=>$row['teacher_image_url']));
                }
                print(json_encode(array_reverse($spiritual_teachers)));
            }else
            {
            }
            $con->close();

        }else{
            print(json_encode(array("PHP EXCEPTION : CAN'T CONNECT TO MYSQL. NULL CONNECTION.")));
        }
    }
    public function handleRequest() {
        if (isset($_POST['name'])) {
            $this->insert();
        } else{
            $this->select();
        }
    }
}
$spirituality=new Spirituality();
$spirituality->handleRequest();
