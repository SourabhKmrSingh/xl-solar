<?php
include_once("inc_config.php");

@$title_id = $validation->urlstring_validate($_GET['id']);
if($title_id == "")
{
    header("Location: {$base_url}error{$suffix}");
    exit();
}
$pageResult = $db->view('*', 'rb_dynamic_pages', 'pageid', "and title_id='$title_id' and status='active'", '', '1');
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

$pageid = $pageRow['pageid'];

$where_query = "";
if($pageid != "")
{
    $where_query .= " and pageid = '$pageid'";
}
$where_query .= " and status='active'";

$table = "rb_dynamic_records";
$id = "recordid";
$orderby = "order_custom desc";
//$url_parameters = "&id=$title_id";
$url = BASE_URL."section/{$title_id}/20/";

$data = $pagination3->main($table, $url_parameters, $where_query, $id, $orderby, $url);
?><!DOCTYPE html>
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
                        <?php
                    if($data['num_rows'] >= 1)
                    {
                        foreach($data['result'] as $sectionRow)
                        {
                            if($sectionRow['url'] == "#")
                            {
                                $section_url = "#";
                                $section_url_target = "";
                            }
                            else if($sectionRow['url'] != "http://www." and $sectionRow['url'] != "https://www." and $sectionRow['url'] != "")
                            {
                                if(substr($sectionRow['url'], 0, 7) == 'http://' || substr($sectionRow['url'], 0, 8) == 'https://')
                                {
                                    $section_url = $validation->db_field_validate($sectionRow['url']);
                                    $section_url_target = $validation->db_field_validate($sectionRow['url_target']);
                                }
                                else
                                {
                                    $section_url = BASE_URL."".$validation->db_field_validate($sectionRow['url']);
                                    $section_url_target = $validation->db_field_validate($sectionRow['url_target']);
                                }
                            }
                            else
                            {
                                $section_url = BASE_URL.'section/'.$title_id.'/'.$validation->db_field_validate($sectionRow['title_id'])."/";
                                $section_url_target = $validation->db_field_validate($sectionRow['url_target']);
                            }
                            
                            $section_date = date('d', strtotime($sectionRow['createdate']));
                            $section_month = date('M', strtotime($sectionRow['createdate']));
                            
                            $section_img = explode(" | ", $sectionRow['imgName']);
                    ?>
                		<div class="col-lg-4">
                            <article class="entry border p-5 mt-5 mb-5">
                                
                                <figure class="entry-media">
                                    <a href="<?php echo $section_url;?>"  target="<?php echo $section_url_target; ?>" title="<?php echo $validation->db_field_validate($sectionRow['title']); ?>">
                                        <?php if($section_img[0] != "" and file_exists(IMG_MAIN_LOC.$section_img[0])) {?>
                                            <img src="<?php echo BASE_URL.IMG_MAIN_LOC.$section_img[0]; ?>" alt="<?php echo $validation->db_field_validate($sectionRow['title']); ?>" title="<?php echo $validation->db_field_validate($sectionRow['title']); ?>" style='max-height: 200px;'>
                                        <?php }else{?>
                                            <img src="<?php echo BASE_URL; ?>images/noimage.jpg" alt="<?php echo $validation->db_field_validate($sectionRow['title']); ?>" title="<?php echo $validation->db_field_validate($sectionRow['title']); ?>" style='max-height: 200px!important;'>
                                        <?php } ?>
                                    </a>
                                </figure><!-- End .entry-media -->
                                
                                <div class="entry-body">
                                    <div class="entry-meta">
                                        <span class="entry-author">
                                            by <a href="<?php echo BASE_URL;?>">XL Solar</a>
                                        </span>
                                        <span class="meta-separator">|</span>
                                        <a href="#"> <?php echo $validation->date_format_custom($sectionRow['createdate']); ?></a>
                                        <span class="meta-separator"></span>
                                    </div><!-- End .entry-meta -->

                                    <h2 class="entry-title">
                                        <a href="<?php echo $section_url;?>"  target="<?php echo $section_url_target; ?>" title="<?php echo $validation->db_field_validate($sectionRow['title']); ?>"><?php echo $validation->db_field_validate($sectionRow['title']); ?></a>
                                    </h2><!-- End .entry-title -->

                                    <!-- <div class="entry-cats">
                                        in <a href="#">Lifestyle</a>,
                                        <a href="#">Shopping</a>
                                    </div> --><!-- End .entry-cats -->

                                    <div class="entry-content">
                                        <p><?php echo $validation->getplaintext($sectionRow['description'],220); ?></p>
                                        <a href="<?php echo $section_url;?>" class="read-more">Continue Reading</a>
                                    </div><!-- End .entry-content -->
                                </div><!-- End .entry-body -->
                            </article><!-- End .entry -->
                		</div><!-- End .col-lg-9 -->
                        <?php
                        }
                    }
                    ?>
                		
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