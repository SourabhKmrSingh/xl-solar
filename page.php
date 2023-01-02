<?php
include_once("inc_config.php");

$pageid = $validation->urlstring_validate($_GET['id']);
$pageResult = $db->view('*', 'rb_pages', 'pageid', "and title_id='$pageid'", '', '1');
if($pageResult['num_rows'] == 0)
{
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];

if($pageRow['url'] != "http://www." and $pageRow['url'] != "https://www." and $pageRow['url'] != "" and $_SESSION['full_url'] != $full_url)
{
    if(substr($pageRow['url'], 0, 7) == 'http://' || substr($pageRow['url'], 0, 8) == 'https://')
    {
        $page_url = $validation->db_field_validate($pageRow['url']);
        $page_url_target = $validation->db_field_validate($pageRow['url_target']);
    }
    else
    {
        $page_url = BASE_URL."".$validation->db_field_validate($pageRow['url']);
        $page_url_target = $validation->db_field_validate($pageRow['url_target']);
    }
    
    $_SESSION['full_url'] = $full_url;
    header("Location: {$page_url}");
    exit();
}
$_SESSION['full_url'] = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php if ($pageRow['meta_title'] != "") { ?>
        <title><?php echo $validation->db_field_validate($pageRow['meta_title']); ?></title>
        <meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['meta_keywords']); ?>" />
        <meta name="description" content="<?php echo $validation->db_field_validate($pageRow['meta_description']); ?>" />
    <?php } else { ?>
        <title><?php echo $validation->db_field_validate($pageRow['title']) . " | ";
                include_once("inc_title.php"); ?></title>
        <meta name="keywords" content="<?php echo $validation->db_field_validate($pageRow['title']); ?>" />
    <?php } ?>
    <?php include_once("inc_files.php"); ?>
</head>
<body>
    <div class="page-wrapper">
    <?php include_once("inc_header.php"); ?>
    <main>
        <div class="page-header text-center" style="background-image: url('<?php echo BASE_URL;?>assets/images/page-header-bg.jpg')">
            <div class="container">
                <h1 class="page-title"><?php echo $validation->db_field_validate($pageRow['title']); ?><!-- <span>Pages</span> --></h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-0">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $validation->db_field_validate($pageRow['title']); ?></li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->
        <section class="blog-details spad pt-4 pb-4">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="blog-details-inner">
                            <div class="blog-large-pic">
                                <?php if($pageRow['imgName'] != "") { ?>
                                    <img src="<?php echo BASE_URL.IMG_MAIN_LOC.$validation->db_field_validate($pageRow['imgName']); ?>" title="<?php echo $validation->db_field_validate($pageRow['title']); ?>" alt="<?php echo $validation->db_field_validate($pageRow['title']); ?>" /><br>
                                <?php } ?>
                            </div>
                            <div class="blog-detail-desc">
                                <?php echo $validation->db_field_validate($pageRow['description']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
            
    </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>