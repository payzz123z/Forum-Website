<?php
    session_start();
    include('connect.php');
    if(isset($_POST["ansubmit"])){
		if(! isset($_SESSION['user'])){
			header("Location: login.php");
		}
		elseif(isset($_SESSION['user'])){
			function valid($data){
				$data = trim(stripslashes(htmlspecialchars($data)));
				return $data;
			}
			$answer = valid($_POST["answer"]);
			if($answer == NULL){
				echo "<script>window.alert('Please Enter something.');</script>";
			}
			else{
				$que = "";
				if($_POST["nul"]==0){
					if(strpos($_POST["preby"],$_SESSION["user"]) === false)
						$que = "update quans set answer=CONCAT(answer,'<h3>AND</h3>".$_POST["answer"]."'), answeredby=CONCAT(answeredby,', @ ".$_SESSION["user"]."') where question like '%".$_POST["question"]."%'";
					else
						$que = "update quans set answer=CONCAT(answer,'<h3>AND</h3>".$_POST["answer"]."'), answeredby = '".$_SESSION["user"]."' where question like '%".$_POST["question"]."%'";
				}
				else
					$que = "update quans set answer='".$_POST["answer"]."', answeredby = '".$_SESSION["user"]."' where question like '%".$_POST["question"]."%'";
				if(mysqli_query($conn,$que)){
					echo "<style>#searchbox{display: none;} #tb{display: block;}</style>";
				}
				else
					echo mysqli_error($conn);
			}
		}
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Forums - Home</title>
		<meta charset="UTF-8">
        <link type="text/css" rel="stylesheet" href="css/style.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="css/material.css">
        <link type="text/css" rel="stylesheet" href="fonts/font.css">
        <link rel="icon" href="images/icon1.png" >
        <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
		
        <style>
            textarea{
                display: none;
                width: 300px;
                height: 50px;
                background: #333;
                color: #ddd;
                padding: 10px;
                margin: 5px 0 -14px; 
            }
            .ans_sub{
                display: none;
                padding: 0 10px;
                height: 30px;
                line-height: 30px;
            }
            .pop{
                display: none;
                text-align: center;
                margin: 151.5px auto;
                font-size: 12px;
            }
        </style>
    </head>
    <body id="_1">
	<div id="preloader"></div>
        <!-- navigation bar -->
        <a href="index.php">
            <div id="log">
				<center>
				<img src="images/rticon.png" alt="Mountain" width="75" height="75">
				</center>
            </div>
        </a>
        <ul id="nav-bar">
            <a href="index.php"><li id="home">Home</li></a>
			<a href="tags.php"><li>Tags</li></a>
            <a href="ask.php"><li>Ask Question</li></a>
			
            <?php 
                if(! isset($_SESSION['user'])){
            ?>
            <a href="login.php"><li>Log In</li></a>
            <a href="signup.php"><li>Sign Up</li></a>
            <?php
                }
                else{
            ?>
            <a href="profile.php"><li>Hi, <?php echo $_SESSION["user"]; ?></li></a>
			<?php
				if($_SESSION["userlevel"] == '0'){
			?>
			<a href="admin_page.php"><li>Admin</li></a>
			<?php
				}
			?>	
            <a href="logout.php"><li>Log Out</li></a>
            <?php
                }
			?>
        </ul>
        
        <!-- content -->
        <div id="content">
            <div id="searchbox">
                <center>
                    <div class="heading">
                        <h1 class="logo"><div id="ntro">Question&Answer</div></h1>
                      
                    </div>
                    <form action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"] ); ?>" method="post" enctype="multipart/form-data" >
                        <input name="text" id="search" type="text" title="Question your Answers" placeholder="Search Forum... ">
                        <i class="material-icons" id="sign">search</i>
                        <input name="submit" type="submit" value="Search" class="up-in" id="qsearch">
                    </form>
                </center>
            </div>
            <div class="pop" id="ta">
                <h1><b style="font-size: 1.5em; margin: -60px auto 10px; display: block;">:(</b>ไม่พบข้อมูลที่ค้นหา</h1>
            </div>
            <div class="pop" id="tb">
                <center><h1><b style="font-size: 1.5em; margin: -60px auto 10px; display: block;">:)</b>ขอบคุณสำหรับคำตอบ</h1></center>
            </div>
            <?php

                if(isset($_POST["submit"])) {
                    function valid($data){
                        $data = trim(stripslashes(htmlspecialchars($data)));
                        return $data;
                    }

                    function check($data){
                        $data = strtolower($data);
                        if( $data != "what" && $data != "how" && $data != "who" && $data != "whom" && $data != "when" && $data != "why" && $data != "which" && $data != "where" && $data != "whose" && $data != "is" && $data != "am" && $data != "are" && $data != "do" && $data != "don't" && $data != "does" && $data != "did" && $data != "done" && $data != "was" && $data != "were" && $data != "has" && $data != "have" && $data != "will" && $data != "shall" && $data != "the" && $data != "i" && $data != "a" && $data != "an" && $data != "we" && $data != "he" && $data != "she" && $data != "")
                            return 1;
                        return 0;
                    }
                    $text = valid($_POST["text"]);
                    if($text == NULL){
                        echo "<script>window.alert('Please Enter something to search.');</script>";
                    }
                    else{
                        $text = preg_replace("/[^A-Za-z0-9ก-๙]/"," ",$text);
                        $words = explode(" ",$text);
						
                        $format = "select * from quans where question like '%";
                        $query = "";
                        foreach($words as $word){
                            if(check($word)){
                                if($query == "")
                                    $query = $format.$word."%'";
                                else
                                    $query .= " union ".$format.$word."%'";
                            }
                        }
                        if(!$query){
                            echo "<script>window.alert('Search appropriate question.');</script>";
                        }
                        else{
                            $r = mysqli_query($conn, $query);
                            if(mysqli_error($conn))
                                echo "<script>window.alert('Some Error Occured. Try Again or Contact Us.');</script>";
                            else if(mysqli_num_rows($r)>0) {
            ?>
                <style>.open{display: block;} </style>
                <center>
                    <div class='open' style='height: auto; margin: 60px auto -135px;'>
                        
                        <div id='topic'>
                            <h2 id='topic-head' style="font-weight: normal; border:none; font-size: 22px;">ผลการค้นหาของคุณสำหรับ'<?php echo $text; ?>' คือ :</h2>
                        </div>

            <?php $n = 1; $nul=0; while( $row = mysqli_fetch_assoc($r) ) { ?>
                        
                        <div id="qa-block">
                            <div class="question">
                                <div id="Q">Q.</div><?php echo $row["question"]."<small id='sml'>Posted By: @".$row['askedby']."</small>"; ?>
								<div id="des">Description :<div id="d"><?php echo $row["des"]."<br>"; ?></div></div>
                            </div>
                            <div class="answer">
                                <?php
                                    if($row["answer"]){
                                        $nul=0;
                                        echo $row["answer"]."<br><small>Reply By: @".$row['answeredby']."</small>";
                                    }
                                    else{
                                        $nul=1;
                                        echo "<em>*** ยังไม่มีคำตอบ ***</em>";
                                    }
                                ?>
                                <form id="f<?php echo $n; ?>" style="margin-bottom: -25px;" action="<?php echo htmlspecialchars( $_SERVER["PHP_SELF"] ); ?>" method="post" enctype="multipart/form-data">
<!--                                    <input type="button" value="Click here to answer." id="ans_b" >-->
									
                                    <label style="margin-bottom: -25px;">
									<a id="ans_b<?php echo $n; ?>" href="#area<?php echo $no; ?>"><u>Submit your answer</u></a>
									</label>
                                    <br>
                                    <script>
                                        $(function(){
                                            $('#ans_b<?php echo $n; ?>').click(function(e){
                                                e.preventDefault();
                                                $('#area<?php echo $n; ?>').css("display","block");
                                                $('#ar<?php echo $n; ?>').css("display","block");
                                                $('#f<?php echo $n; ?>').css("margin-bottom","0px");
                                            });
                                        });
                                    </script>
                                    <textarea id="area<?php echo $n; ?>" name="answer" placeholder="Your Answer..."></textarea>
                                    <input style="display: none;" name="question" value="<?php echo $row['question'] ?>">
                                    <input style="display: none;" name="nul" value="<?php echo $nul ?>">
                                    <input style="display: none;" name="preby" value="<?php echo $row['answeredby'] ?>">
                                    <br>
                                    <input type="submit" name="ansubmit" value="Submit" class="up-in ans_sub" id="ar<?php echo $n; ?>">
                                    
                                </form>
                                
       
                            </div>
                        </div>
                            <?php $n++; } ?>
                    </div>
                </center>
            <?php     
                        } // if for no. of rows
                        else{
                            echo "<style>#searchbox{display: none;} #ta{display: block;}</style>";
                        }
                        }
                    } // a non null if
                } // isset for submit
            ?>
			
			
        </div>
		<!-- content -->
		<script src="js/loader.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js%22%3E
</script>
        
        
    </body>
    
</html>
