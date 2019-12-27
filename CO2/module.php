<?php
	class CO2 extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();


			// Register some required parameters for our Corrently CO2 Reading operation
 			$this->RegisterPropertyString("Postleitzahl", "69256");

			$randstr = bin2hex(random_bytes(5));
			$this->RegisterPropertyString("meterId", md5(time())."_".$randstr);

			$secret = bin2hex(random_bytes(10));
			$this->RegisterPropertyString("secret",$secret);

		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
		}

	}
