<?php
	class CO2 extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$meterid = bin2hex(random_bytes(40));
			$secret = bin2hex(random_bytes(40));

			// Register some required parameters for our Corrently CO2 Reading operation
			$i_plz= $this->RegisterVariableString("Postleitzahl", "Postleitzahl");
			$i_meterid= $this->RegisterVariableString("meterId", "meterId");
			$i_secret = $this->RegisterVariableString("secret", "secret");


 			$this->SetValue($i_plz, "69256");
			$this->SetValue($i_meterid, $meterid);
			$this->SetValue($i_secret, $secret);

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
