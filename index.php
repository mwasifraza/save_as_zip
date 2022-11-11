<?php
    if(isset($_POST["submit"])){     
        if(isset($_FILES['images']['name'])){
            $errors = array();
            $all_imgs = array();
            $allowed_ext = ["jpg","png","jpeg"];

            $file_name = $_FILES["images"]["name"];
            $file_tmp = $_FILES["images"]["tmp_name"];
            $file_size = $_FILES["images"]["size"];
            $file_type = $_FILES["images"]["type"];

            foreach($file_name as $key=>$val){
                $get_name = explode('.', $val);
                $file_ext = end($get_name);

                if(in_array($file_ext, $allowed_ext) === false){
                    $errors[] = "Only images are allowed!";
                }
                if($file_size[$key] > 2097152){
                    $errors[] = "File size must be 2MB or lower!";
                }
                if(empty($errors) == true){
                    if(!is_dir("upload")){
                        mkdir("upload");
                    }
                    move_uploaded_file($file_tmp[$key], "upload/".$val);
                    $all_imgs[] = $val;
                    echo "<img src='upload/".$val."' width='300'>";
                }else{
                    foreach($errors as $e){
                        echo "<span style='color:red;'>".$e."</span><br>";
                    }
                    die();
                }
            }   
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Task</title>
<style>
body{ padding: 0; margin: 0; }
img{ margin: 10px; }
section{ margin: 10px; }
</style>
</head><body>

<section>

    <form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
        <br>
        <input type="file" name="images[]" id="images" multiple>
        <input type="submit" name="submit" value="Submit">
    </form>
    <br>

    <?php if(!empty($all_imgs)){ ?>
    <button onclick="downloadAsZip()">Download</button>
    <?php } ?>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js"></script>
<script>

<?php if(!empty($all_imgs)){ ?>
    function downloadAsZip(){
        let urls = <?php echo json_encode($all_imgs); ?>;

        const zip = new JSZip();
        const folder = zip.folder("images"); // folder name where all files will be placed in 

        urls.forEach((url) => {
            let imgname = 'upload/' + url;
            const blobPromise = fetch(imgname).then((r) => {
                if (r.status === 200) return r.blob();
                return Promise.reject(new Error(r.statusText));
            });
            folder.file(url, blobPromise);
        });
        zip.generateAsync({ type: "blob" }).then((blob) => saveAs(blob, "archive.zip"));
    }
<?php } ?>

</script>
</body></html>