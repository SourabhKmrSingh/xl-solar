<?php
include_once("inc_config.php");

@$pageid = $validation->urlstring_validate($_GET['pageid']);
if ($pageid == "") {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageResult = $db->view('*', 'rb_dynamic_pages', 'pageid', "and title_id='$pageid' and status='active'", '', '1');
if ($pageResult['num_rows'] == 0) {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageRow = $pageResult['result'][0];
$pageid = $pageRow['pageid'];

@$title_id = $validation->urlstring_validate($_GET['id']);
if ($title_id == "") {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$sectionResult = $db->view('*', 'rb_dynamic_records', 'recordid', "and pageid='$pageid' and title_id='$title_id' and status='active'", '', '1');
if ($sectionResult['num_rows'] == 0) {
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$sectionRow = $sectionResult['result'][0];
$recordid = $sectionRow['recordid'];
$section_img = explode(" | ", $sectionRow['imgName']);

$prevrecordResult = $db->view('*', 'rb_dynamic_records', 'recordid', "and pageid='$pageid' and recordid = (select max(recordid) from rb_dynamic_records where recordid < $recordid) and status='active'", '', '1');
$prevrecordRow = $prevrecordResult['result'][0];

$nextrecordResult = $db->view('*', 'rb_dynamic_records', 'recordid', "and pageid='$pageid' and recordid = (select min(recordid) from rb_dynamic_records where recordid > $recordid) and status='active'", '', '1');
$nextrecordRow = $nextrecordResult['result'][0];
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

            <div class="page-content">
                <div class="container">
                	<div class="row">
                		<div class="col-lg-8 offset-lg-2 mt-5">
                            <article class="entry single-entry">
                                <?php if ($section_img[0] != "" and file_exists(IMG_MAIN_LOC . $section_img[0])) { ?>
                                    <figure class="entry-media">
                                        <img src="<?php echo BASE_URL . IMG_MAIN_LOC . $section_img[0]; ?>" title="<?php echo $validation->db_field_validate($sectionRow['title']); ?>" alt="<?php echo $validation->db_field_validate($sectionRow['title']); ?>" class="w-75" />
                                    </figure><!-- End .entry-media -->
                                <?php } ?>
                                <div class="entry-body">
                                    <div class="entry-meta">
                                        <span class="entry-author">
                                            by <a href="<?php echo BASE_URL;?>">Sunlief</a>
                                        </span>
                                        <span class="meta-separator">|</span>
                                        <a href="#"><?php echo $validation->date_format_custom($sectionRow['createdate']); ?> (<?php echo $validation->timecount("{$sectionRow['createdate']} {$sectionRow['createtime']}"); ?>)</a>
                                        
                                    </div><!-- End .entry-meta -->

                                    <h2 class="entry-title">
                                        <?php echo $validation->db_field_validate($sectionRow['title']); ?>
                                    </h2><!-- End .entry-title -->

                                    <div class="entry-cats">
                                        <?php echo $validation->db_field_validate($sectionRow['tagline']); ?>
                                    </div><!-- End .entry-cats -->

                                    <div class="entry-content editor-content">
                                        <p<?php echo $validation->db_field_validate($sectionRow['description']); ?></p>
                                    </div><!-- End .entry-content -->

                                    <div class="entry-footer row no-gutters flex-column flex-md-row">
                                        

                                        <div class="col-md-auto mt-2 mt-md-0">
                                            <div class="social-icons social-icons-color">
                                                <span class="social-label">Share this post:</span>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $full_url; ?>&title=<?php echo $validation->db_field_validate($sectionRow['title']); ?>" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                                                <a href="http://twitter.com/share?text=<?php echo $validation->db_field_validate($postRow['title']); ?>&url=<?php echo $full_url; ?>" class="social-icon social-twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                                                <a href="http://pinterest.com/pin/create/button/?url=<?php echo $full_url; ?>&media=<?php echo BASE_URL . IMG_MAIN_LOC . $validation->db_field_validate($sectionRow['imgName']); ?>" class="social-icon social-pinterest" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
                                                <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $full_url; ?>" class="social-icon social-linkedin" title="Linkedin" target="_blank"><i class="icon-linkedin"></i></a>
                                            </div><!-- End .soial-icons -->
                                        </div><!-- End .col-auto -->
                                    </div><!-- End .entry-footer row no-gutters -->
                                </div><!-- End .entry-body -->

                            </article><!-- End .entry -->


                	</div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .page-content -->
       </main>
<?php include_once("inc_footer.php");?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
<!-- Plugins JS File -->
<?php include_once("inc_files_bottom.php");?>
</body>
</html>