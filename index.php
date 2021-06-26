<?php 


function OpenCon()
 {
    $dbhost = "127.0.0.1" ;
    $dbuser = "root" ;
    $dbpass = "root" ;
    $db = "TesMember";
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connect failed: %s\n". $conn -> error);
    
    return $conn;
 }

  
function CloseCon($conn)
{
    $conn->close();
}


function get_tree($name = "") { 

    $conn = OpenCon() ; 
    $arrayresult = array() ;
    $id = 0 ; 
    $tree = "" ;

    $sql = "SELECT * FROM  member WHERE name like  '%$name%' ";
    $result = $conn -> query($sql) ;
    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $arrayresult['name'] =  $row['name'] ;
            $id = $row["id"] ;  
        }
        
        $sql2 = "SELECT * FROM  member WHERE  parent_id = $id " ;
        $result2 = $conn -> query($sql2) ;

        if ($result2->num_rows > 0) {
            
            $i = 0 ;
            $id2 = 0 ; 
            $arraychild = array() ;

            while($row = $result2->fetch_assoc()) {

                $arraychild[$i]['name'] = $row['name']  ;
                $arraychild[$i]['children'] = array()  ;  
                $id2 = $row["id"] ;  
                $i++ ;
            
            }

            $sql3 = "SELECT * FROM  member WHERE  parent_id = $id2 " ;
            $result3 = $conn -> query($sql3) ;

            if ($result3->num_rows > 0) {
                
                $i = 0 ;
                $id3 = 0 ; 
                $arraychild2 = array() ;

                while($row = $result3->fetch_assoc()) {

                    $arraychild2[$i]['name'] = $row['name']  ;
                    $arraychild2[$i]['children'] = array()  ;  
                    $id3 = $row["id"] ;  
                    $i++ ;
                
                }

                $arraychild = $arraychild2 ;
                $arrayresult['children'] = $arraychild ;
                $tree =  json_encode($arrayresult)  ; 
                
            }else{
                $arrayresult['children'] = $arraychild ; 
                $tree =  json_encode($arrayresult)  ; 
            }
            
        }else{
            $arrayresult['children'] = array() ; 
            $tree = json_encode($arrayresult)  ; 
        }

    } else {
        $tree = json_encode(array());
    }

    CloseCon($conn) ; 

    return $tree;
}




function get_parents($name) { 

    $conn = OpenCon() ; 
    $arrayresult = array() ;
    $sql = "SELECT parent_id FROM  member WHERE name like  '%$name%' ";
    $result = $conn->query($sql) ;

    if($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {  
            $id = $row["parent_id"] ;     
        }
    
        $sql2 = "SELECT * FROM  member WHERE parent_id =  $id" ;
        $result2 = $conn->query($sql2) ;
        $name1 = "" ;

        if($result2->num_rows > 0) {
            
            while($row = $result2->fetch_assoc()) {  
                $name1 = $row["name"] ;     
                $id2 = $row["parent_id"] ;     
            }

            $sql3 = "SELECT * FROM  member WHERE parent_id =  $id2" ;
            $result3 = $conn->query($sql3) ;

            if($result3->num_rows > 0){

                while($row = $result3->fetch_assoc()) {  
                    $name2 = $row["name"] ;     
                    $id3 = $row["parent_id"] ;     
                }
    
                array_push($arrayresult , $name2 );

            }else{
                array_push($arrayresult , $name1 );
            }

        }else{
            $arrayresult = array() ;
        } 
        
    }else{
        $arrayresult  = array() ;
    }
    CloseCon($conn) ;
    return  $arrayresult ; 
}

function get_children($name) {
    
    $conn = OpenCon() ; 
    $arrayresult = array() ;
    $sql = "SELECT id FROM  member WHERE name like  '%$name%' ";
    $result = $conn->query($sql) ;
    $id = 0;
    $i = 0 ;
    if($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {  
            $id = $row["id"] ;
        }

        $sql2 = "SELECT name FROM  member WHERE parent_id =  $id" ;
        $result2 = $conn->query($sql2) ;

        while($row = $result2->fetch_assoc()) {  
            $arrayresult[$i] = $row['name'] ;
            $i++ ;
        }

    }else{
        $arrayresult = array() ;
    }

    CloseCon($conn) ;
    return  $arrayresult ; 
}


$tree = get_tree("root");
echo $tree ; 


$parents = get_parents('Derpina');
echo json_encode($parents);

$children = get_children("John");
echo json_encode($children);





?>