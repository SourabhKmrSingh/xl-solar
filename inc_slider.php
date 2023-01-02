<div class="intro-slider-container mb-4">
    <div class="intro-slider owl-carousel owl-simple owl-nav-inside" data-toggle="owl" data-owl-options='{
            "nav": false, 
            "dots": true,
            "responsive": {
                "992": {
                    "nav": true,
                    "dots": false
                }
            }
        }'>
        <?php
        $sliderResult = $db->view('sliderid,title,title_id,url,url_target,imgName,tagline,description,sale', 'rb_sliders', 'sliderid', "and status='active'", 'order_custom desc', '8');
        if($sliderResult['num_rows'] >= 1)
        {
            foreach($sliderResult['result'] as $sliderRow)
            {
                if($sliderRow['url'] != "http://www." and $sliderRow['url'] != "")
                {
                    $slider_url = $sliderRow['url'];
                    $slider_target = $sliderRow['url_target'];;
                }
                else
                {
                    $slider_url = "";
                    $slider_target = "";
                }
        ?>
        <div class="intro-slide" style="background-image: url(<?php echo BASE_URL . IMG_MAIN_LOC . $sliderRow['imgName'];?>);">
            <div class="container intro-content">
                <h3 class="intro-subtitle text-primary"><?php echo $validation->db_field_validate($sliderRow['tagline']); ?></h3><!-- End .h3 intro-subtitle -->
                <h1 class="intro-title"><?php echo $validation->db_field_validate($sliderRow['title']); ?></h1><!-- End .intro-title -->
                <a href="<?php echo $slider_url; ?>" target="<?php echo $slider_target; ?>" class="btn btn-outline-primary-2 min-width-sm">
                    <span>SHOP NOW</span>
                    <i class="icon-long-arrow-right"></i>
                </a>
               
            </div><!-- End .intro-content -->
        </div><!-- End .intro-slide -->
         <?php
            }
        }
        ?>
    </div><!-- End .intro-slider owl-carousel owl-simple -->
    <span class="slider-loader"></span><!-- End .slider-loader -->
</div><!-- End .intro-slider-container -->