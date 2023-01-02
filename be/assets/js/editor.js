// When the user clicks the button, open the modal
$(document).ready(function(){
	// Get the modal
	var image_model = $('#image_model');

	// Get the button that opens the modal
	var image_model_button = $("#image_model_button");
	
	// Get the <span> element that closes the modal
	var image_close_button = $(".image_close_button");

	var image_close = $('#image_close');
	
	image_model_button.click(function(){
		image_model.fadeIn(100);
		/*modal.animate({ fontSize: '15px' });*/
	});

	image_close_button.click(function(){
		image_model.fadeOut(100);
	});

	image_close.click(function(){
		image_model.fadeOut(100);
	});
});

//For Image
$(document).ready(function() {
	$("#drop-area").on('dragenter', function (e){
	e.preventDefault();
	$(this).css('background', '#BBD5B8');
	});

	$("#drop-area").on('dragover', function (e){
	e.preventDefault();
	});

	$("#drop-area").on('drop', function (e){
	$(this).css('background', '#D8F9D3');
	e.preventDefault();
	var image = e.originalEvent.dataTransfer.files;
	createFormData(image);
	});
});

function createFormData(image) {
	var formImage = new FormData();
	formImage.append('userImage', image[0]);
	var imgType = image[0].type;
	var imgSize = image[0].size;
	if(imgSize > 5242880)
	{
		$('#drop-area').find(".image_upper_text").hide();
		$('#drop-area').find(".image_upper_text").fadeIn().html("<i class='fa fa-times' aria-hidden='true' style='color:red;'></i> Image Size must be under 5 MB!");
	}
	else if(imgType != "image/jpeg" && imgType != "image/png" && imgType != "image/jpg" && imgType != "image/gif")
	{
		$('#drop-area').find(".image_upper_text").hide();
		$('#drop-area').find(".image_upper_text").fadeIn().html("<i class='fa fa-times' aria-hidden='true' style='color:red;'></i> Please upload image only!!!");
	}
	else
	{
		uploadFormData(formImage);
	}
}

function uploadFormData(formData) {
	$('#drop-area').find(".image_upper_text").hide();
	$('#drop-area').find(".image_model_loader").show();
	$.ajax({
		url: "upload.php",
		type: "POST",
		data: formData,
		contentType:false,
		cache: false,
		processData: false,
		success: function(data){
			$('#drop-area').find(".image_model_loader").fadeOut();
			$('#drop-area').find(".image_upper_text").fadeIn().html("<i class='fa fa-check' aria-hidden='true' style='color: #0BC414;'></i> Your Image has been Uploaded. Upload more pictures!!!");
			tinymce.editors[0].execCommand('mceInsertContent', false, data);
		},
		error: function(error)
		{
			alert(" Can't do because: " + error);
		}
	});
}

//For upload choose file image
function uploadimage(input)
{
	var myFormData = new FormData();
	myFormData.append('userImage', input.files[0]);
	var imgType = input.files[0].type;
	var imgSize = input.files[0].size;
	if(imgSize > 5242880)
	{
		$('#drop-area').find(".image_upper_text").hide();
		$('#drop-area').find(".image_upper_text").fadeIn().html("<i class='fa fa-times' aria-hidden='true' style='color:red;'></i> Image Size must be under 5 MB!");
	}
	else if(imgType != "image/jpeg" && imgType != "image/png" && imgType != "image/jpg" && imgType != "image/gif")
	{
		$('#drop-area').find(".image_upper_text").hide();
		$('#drop-area').find(".image_upper_text").fadeIn().html("<i class='fa fa-times' aria-hidden='true' style='color:red;'></i> Please upload image only!!!");
	}
	else
	{
		$('#drop-area').find(".image_upper_text").hide();
		$('#drop-area').find(".image_model_loader").show();
		$.ajax({
			url: "upload.php",
			enctype: 'multipart/form-data',
			type: "POST",
			data: myFormData,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data)
			{
				$('#drop-area').find(".image_model_loader").fadeOut();
				$('#drop-area').find(".image_upper_text").fadeIn().html("<i class='fa fa-check' aria-hidden='true' style='color: #0BC414;'></i> Your Image has been Uploaded. Upload more pictures!!!");
				tinymce.editors[0].execCommand('mceInsertContent', false, data);
			},
			error: function(request, error)
			{
				alert(" Can't do because: " + error);
			}
		});
	}
}

tinymce.init({
	selector:'.tinymce',
	height:'400',
	menubar: false,
	plugins: [
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"insertdatetime media nonbreaking save table contextmenu directionality",
		"emoticons template paste textcolor colorpicker textpattern spellchecker"
	],
	toolbar1: "insertfile undo redo | styleselect | fontselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | hr link unlink image | print preview media | forecolor backcolor emoticons spellchecker searchreplace | visualchars blockquote charmap table | removeformat fullscreen code",
	extended_valid_elements : "script[language|type|async|src|charset]",
	valid_children : "+body[style], +style[type]",
	valid_elements : '*[*]',
	image_advtab: true,
	image_title: true,
	relative_urls : false,
	remove_script_host : false,
	entity_encoding: "raw",
	apply_source_formatting : false,
	verify_html : false,
	convert_urls : false
});