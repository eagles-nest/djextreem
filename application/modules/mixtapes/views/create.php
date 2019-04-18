<?php 
if(isset($error)){
	foreach ((array)$error as $key => $value) {
		echo $value;
	}
}
if(isset($flash)){
echo $flash;}
if(is_numeric($mixtape_id)) {?>
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header" data-original-title>
			<h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Options</h2>
			<div class="box-icon">
				<a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
				<a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
			</div>
		</div>
		<div class="box-content">
			<?php if($big_pic ==""){?>
			<a href="<?= base_url() ?>mixtapes/upload_image/<?=$mixtape_id?>"><button type="button" class="btn btn-primary">Upload Mixtape Image</button></a>
			<?php } else{ ?>
			<a href="<?= base_url() ?>mixtapes/delete_image/<?=$mixtape_id?>"><button type="button" class="btn btn-danger">Delete Mixtape Image</button></a>
			<?php } ?>
			<a href="<?= base_url() ?>mixtapes/deleteconf/<?= $mixtape_id?>"><button type="button" class="btn btn-danger">Delete Mixtape</button></a>
			<a href="<?= base_url() ?>mixtapes/view/<?= $mixtape_id?>"><button type="button" class="btn btn-default">View Mixtape In Site</button></a>
		</div>
	</div><!--/span-->
</div><!--/row-->
<?php } ?>

<h1><?= $headline ?></h1>
<?= validation_errors("<p style='color:red;'>", "</p>") ?>
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header" data-original-title>
			<h2><i class="halflings-icon white edit"></i><span class="break"></span>Mixtape Details</h2>
			<div class="box-icon">
				<a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
				<a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
			</div>
		</div>
		<div class="box-content">
		    <?php
		    $form_location=base_url()."mixtapes/create/".$mixtape_id;
		    $attributes=array('class' =>'form-horizontal');
			//echo form_open_multipart('mixtapes/create/'.$mixtape_id,$attributes);
			?>
			<form class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?= $form_location ?>">
			  <fieldset>
				<div class="control-group">
				  <label class="control-label" for="typeahead">Mixtape Title </label>
				  <div class="controls">
					<input type="text" class="span6" name="mixtape_title" value="<?= $mixtape_title?>" >
				  </div>
				</div>
				<div class="control-group">
				  <label class="control-label" for="typeahead">Mixtape Link </label>
				  <div class="controls">
					<input type="text" class="span6" name="mixtape_link" value="<?= $mixtape_link ?>" >
				  </div>
				</div>
				<?php 
				if(!isset($mixtape_id)){ ?>
				<div class="control-group">							
					<label class="control-label" for="fileInput">Mixtape File input</label>
					<div class="controls">
						<input class="input-file uniform_on" id="fileInput" name="mixtape_file" type="file">
					</div>
				</div>
				<?php } ?>
				<div class="control-group">
				    <label class="control-label" for="typeahead">Mixtape Type </label>
				    <div class="controls">
				    	<?php
				    	//status=1 or 0
				    	$additional_dd_code='id="selectError3"';
				    	$options = array(
				    	     ''=>'Please Select ...',
				    		'1'=> 'Video',
				    		'2'=> 'Audio',);
				    	$shirts_on_sale=array('small','large');
				    	echo form_dropdown('mixtape_type',$options,$mixtape_type,$additional_dd_code); 
				    	?>
				    </div>
				</div>		          
				<div class="control-group hidden-phone">
				  <label class="control-label" for="textarea2">Mixtape Description</label>
				  <div class="controls">
					<textarea class="cleditor" id="textarea2" rows="3" name="mixtape_description"><?php echo $mixtape_description;?></textarea>
				  </div>
				</div>
				<div class="form-actions">
				  <button type="submit" class="btn btn-primary" name="submit" value="Submit">Submit</button>
				  <button type="submit" class="btn" name="submit" value="Cancel">Cancel</button>
				</div>
			  </fieldset>
			</form>
		</div>
	</div><!--/span-->
</div><!--/row-->
