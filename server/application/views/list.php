<!doctype html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<title>iOS crash report</title>
	<style type="text/css">
	html {font-size: 12px;}
#customers {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    width: 1350px;
    border-collapse: collapse;
    text-align: center;
}

#customers td, #customers th {
    font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;
    font-size: 12px;
}

#customers th {
    font-size: 12px;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #A7C942;
    color: #ffffff;
}

#customers tr.alt td {
    color: #000000;
    background-color: #EAF2D3;
}
	</style>
</head>
<body>
	<h1>iOS crash report list:</h1>
	<p>
	<?php echo form_open_multipart('web/upload');?>
		<input type="file" name="userfile" />
		<input type="submit" value="upload" />
	</form>
	<p> 
	<table id="customers"> 
		<tr>
			<th>ID</th>
			<th>Date/Time</th>
			<th>UUID</th>
			<th>原始报告</th>
			<th>解析后报告</th>			
			<th>Hardware Model</th>
			<th>OS Version</th>
			<th>Incident Identifier</th>
			<th>CrashReporter Key</th>
		</th>
		<?php if (count($list) > 0) { ?>
		<?php foreach ($list as $log) { ?>
    	<tr> 
	     	<td><?php echo $log->id; ?></td>
			<td><?php echo $log->report_time; ?></td>
			<td><?php echo $log->uuid; ?></td>
			<td><a href="/web/origin_report/<?php echo $log->id; ?>" target="_blank">查看</a></td>
			<td>
				<?php if (!($log->report)) { ?>
					尚未解析
				<?php } else { ?>
					<a href="/web/view_report/<?php echo $log->id; ?>" target="_blank">查看</a>
				<?php } ?>
			</td>			
			<td><?php echo $log->device; ?></td>
			<td><?php echo $log->ios_version; ?></td>
			<td><?php echo $log->report_id; ?></td>
			<td><?php echo $log->report_key; ?></td>
	   	</tr>
		<?php } ?>

	<?php } ?>
	</table>
</body>
</html>