<?php
class CorrentlyAppConnector extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->RegisterPropertyString("Postleitzahl", "69256");
			$this->RegisterPropertyString("ac", "");
			$this->RegisterPropertyString("wc", "");
			$this->RegisterPropertyInteger("Nennleistung",0);
			$this->RegisterPropertyInteger("Zyklusverbrauch",0);
			$this->RegisterPropertyInteger("meteringvariable", 0);

			if(!IPS_GetVariableProfile("Watt-Stunden")) {
			    IPS_CreateVariableProfile ("Watt-Stunden", 1);
			    IPS_SetVariableProfileText("Watt-Stunden","","Wh");
			    IPS_SetVariableProfileIcon("Watt-Stunden",  "Electricity");
			}
			if(!IPS_GetVariableProfile("Watt")) {
			    IPS_CreateVariableProfile ("Watt", 1);
			    IPS_SetVariableProfileText("Watt","","Wh");
			    IPS_SetVariableProfileIcon("Watt",  "Electricity");
			}
		}

		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			IPS_LogMessage("MessageSink", "Event Info ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true));

			$zip = $this->ReadPropertyString("Postleitzahl");
			$ac = $this->ReadPropertyString("ac");
			$wc = $this->ReadPropertyString("wc");
			$event = 0;

	    $parent = IPS_GetParent($SenderID);
	    $label = IPS_GetName($parent);

	    if((!$Data[0])&&($Data[0]<2)) {
	        // Behandlung von Switches (beim Ausschalten)
	        if((GetValueInteger($this->GetIDForIdent("Zyklusverbrauch_".$parent)) > 0)&&(GetValueInteger($this->GetIDForIdent("Nennleistung_".$parent)) == 0)) {
							$event_dauer = time() - GetValue($this->GetIDForIdent("Startzeit_".$parent));
	            $event = GetValueInteger($this->GetIDForIdent("Zyklusverbrauch_".$parent));
	            SetValue($this->GetIDForIdent("Betriebszeit_".$parent),GetValue($this->GetIDForIdent("Betriebszeit_".$parent)) + $event_dauer);
	        } else {
	          if(($this->GetIDForIdent("Betriebszeit_".$parent))&&($this->GetIDForIdent("Nennleistung_".$parent))&&(@IPS_GetVariableIDByName('Startzeit',$parent))) {
	             $event_dauer = time() - GetValue(@IPS_GetVariableIDByName('Startzeit',$parent));
	             $event = round((GetValue($this->GetIDForIdent("Nennleistung_".$parent))/3600) * $event_dauer) + GetValue($this->GetIDForIdent("Zyklusverbrauch_".$parent));
	             SetValue($this->GetIDForIdent("Betriebszeit_".$parent),GetValue($this->GetIDForIdent("Betriebszeit_".$parent)) + $event_dauer);
	             SetValue($this->GetIDForIdent("Zyklusverbrauch_".$parent), $event);
	          } else {

	           }
	          }
	        } else {
	            if($Data[0]==1) {
	                 // Behandlung von Switches zum Zeitpunkt des Einschalten
	                    SetValue($this->GetIDForIdent("Startzeit_".$parent),time());
	                    echo "Gestartet";
	               			SetValue($this->GetIDForIdent("Betriebszeit_".$parent), 0);
	            } else {
								/* Removed as of v50 (No Meter Support until Variable Handling change is completed)
	                if(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent)) {
	                    // Übermitteln wenn dieser Zählerstand mindestens 50wh größer als letzter Zählerstand

	                    if(GetValue(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent)) < $_IPS["VALUE"] - 50) {
	                        $event = $Data[0] - GetValue(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent));
	                        SetValue(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent),$_IPS["VALUE"]);
	                    }

	                } else {
	                    $i = IPS_CreateVariable(1);
	                    IPS_SetParent($i,$parent);
	                    IPS_SetName($i,'Letzter Zählerstand');
	                    IPS_SetVariableCustomProfile($i,"Watt-Stunden");
	                    SetValue($i,$Data[0]);
	                }
									*/
	            }
	        }
	        if($event >0) {
	            SetValue($this->GetIDForIdent("Gesamtverbrauch_".$parent),GetValue($this->GetIDForIdent("Gesamtverbrauch_".$parent)) + $event);
	        }
			// Handling of Variables

			// Minor Usage Treshhold - Wenn weniger als 10Wh Verbraucht werden, macht eine Übermittlung an die App keinen Sinn!
			if($event > 10) {
				  if($this->GetIDForIdent("Zyklusverbrauch_".$parent)) {
						if(GetValue($this->GetIDForIdent("Nennleistung_".$parent)) > 0) {
							SetValue($this->GetIDForIdent("Zyklusverbrauch_".$parent),0);
						}
					}

			    $postData = array(
			        'event' => array(
			            "ac" => $ac,
			            "wc" => $wc,
			            "event" => $event,
			            "zip" => $zip,
			            "l" => $label
			        )
			    );
			    $ch = curl_init('https://api.corrently.io/core/event/');
			    curl_setopt_array($ch, array(
			        CURLOPT_POST => TRUE,
			        CURLOPT_RETURNTRANSFER => TRUE,
			        CURLOPT_HTTPHEADER => array(
			            'Content-Type: application/json'
			        ),
			        CURLOPT_POSTFIELDS => json_encode($postData)
			    ));

			    $response = curl_exec($ch);

			    $responseData = json_decode($response, TRUE);
					print_r($responseData);
			}
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
			if($this->ReadPropertyInteger("meteringvariable")!=0) {
						$parent = IPS_GetParent($this->ReadPropertyInteger("meteringvariable"));
						$label = IPS_GetName($parent);

						$this->RegisterVariableInteger("Startzeit_".$parent, $label." Starzeit");
						$this->RegisterVariableInteger("Gesamtverbrauch_".$parent, $label." Gesamtverbrauch");
						$this->RegisterVariableInteger("Betriebszeit_".$parent, $label." Betriebszeit");

						SetValue($this->RegisterVariableInteger("Nennleistung_".$parent, $label." Nennleistung","Watt"),$this->ReadPropertyInteger("Nennleistung"));
						SetValue($this->RegisterVariableInteger("Zyklusverbrauch_".$parent, $label." Zyklusverbrauch","Watt-Stunden"),$this->ReadPropertyInteger("Zyklusverbrauch"));

						$this->RegisterMessage($this->ReadPropertyInteger("meteringvariable"), 10603 /* IM_CHANGESTATUS */);
			}
		}
}
