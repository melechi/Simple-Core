<html>
	<head>
		<title>Simple Core v{VERSION} - Error Report</title>
		<style type="text/css">
		body{
			background-color: #FFF;
			color: #000;
			font-size: 14px;
		}
		#errorContainer{
			background-color: #CCC;
			border: 1px sold #000;
		}
		#errorHeading{
			font-size: 16px;
			font-weight: bold;
			padding: 4px;
		}
		#errorTable{
			padding: 1px;
		}
		#errorTable td{
			padding: 2px;
		}
		#debugDump{
			width: 800px;
			height: 300px;
			overflow: auto;
			background-color: #616D7E;
		}
		.rowTitle{
			background-color: #151B54;
			color: #FFF;
			font-weight: bold;
		}
		.rowInfo{
			background-color: #5E767E;
		}
		</style>
	</head>
	<body>
		<div id="errorContainer">
			<span id="errorHeading">Your Error Report</span><br />
			{ERRORS}
		</div>
	</body>
</html>