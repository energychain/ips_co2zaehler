<?php
class CorrentlyAppConnector extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->RegisterPropertyString("Postleitzahl", "69256");
			$this->RegisterPropertyString("ac", "");
			$this->RegisterPropertyString("wc", "");
			$this->RegisterPropertyInteger("meteringvariable", 0);
			/*
			$this->RegisterPropertyString("Postleitzahl", "69256");
			$this->RegisterPropertyString("meterId", $meterid);
			$this->RegisterPropertyString("secret", $secret);
		x

			$this->RegisterVariableInteger("reading_in_wh", "Zählerstand aktuell (in Wh)");
			$this->RegisterVariableInteger("co2g_standard", "CO2 (Standard)");
			$this->RegisterVariableInteger("co2g_oekostrom", "CO2 (Ökostrom)");
			$this->RegisterVariableString("account", "Kompensations Account");
			*/

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

		public function update() {
			$zip = $this->ReadPropertyString("Postleitzahl");
			$ac = $this->ReadPropertyString("ac");
			$wc = $this->ReadPropertyString("wc");

			if($_IPS['SENDER'] == 'Variable') {
			    $parent = IPS_GetParent($_IPS['VARIABLE']);
			    $label = IPS_GetName($parent);

			    if((!$_IPS['VALUE'])&&($_IPS['VALUE']<2)) {
			        // Behandlung von Switches (beim Ausschalten)
			        if((@IPS_GetVariableIDByName('Zyklusverbrauch',$parent))&&(GetValueInteger(@IPS_GetVariableIDByName('Zyklusverbrauch',$parent)) > 0)) {
			            $event_dauer = time() - GetValue(@IPS_GetVariableIDByName('Startzeit',$parent));
			            $event = GetValueInteger(@IPS_GetVariableIDByName('Zyklusverbrauch',$parent));
			            SetValue(@IPS_GetVariableIDByName('Betriebszeit',$parent),GetValue(@IPS_GetVariableIDByName('Betriebszeit',$parent)) + $event_dauer);
			        } else {
			          if((@IPS_GetVariableIDByName('Betriebszeit',$parent))&&(@IPS_GetVariableIDByName('Nennleistung',$parent))&&(@IPS_GetVariableIDByName('Startzeit',$parent))) {
			             $event_dauer = time() - GetValue(@IPS_GetVariableIDByName('Startzeit',$parent));
			             $event = round((GetValue(@IPS_GetVariableIDByName('Nennleistung',$parent))/3600) * $event_dauer);
			             SetValue(@IPS_GetVariableIDByName('Betriebszeit',$parent),GetValue(@IPS_GetVariableIDByName('Betriebszeit',$parent)) + $event_dauer);
			             SetValue(@IPS_GetVariableIDByName('Zyklusverbrauch',$parent), $event);
			          } else {
			            $i = IPS_CreateVariable(1);
			            IPS_SetParent($i,$parent);
			            IPS_SetName($i,"Startzeit");
			            $i = IPS_CreateVariable(1);
			            IPS_SetParent($i,$parent);
			            IPS_SetName($i,"Betriebszeit");
			            $i = IPS_CreateVariable(1);
			            IPS_SetParent($i,$parent);
			            IPS_SetName($i,"Nennleistung");
			            IPS_SetVariableCustomProfile($i,"Watt");
			            $i = IPS_CreateVariable(1);
			            IPS_SetParent($i,$parent);
			            IPS_SetName($i,"Zyklusverbrauch");
			            IPS_SetVariableCustomProfile($i,"Watt-Stunden");
			            $i = IPS_CreateVariable(1);
			            IPS_SetParent($i,$parent);
			            IPS_SetName($i,"Gesamtverbrauch");
			            IPS_SetVariableCustomProfile($i,"Watt-Stunden");
			            }
			          }
			        } else {
			            if($_IPS['VALUE']==1) {
			                 // Behandlung von Switches zum Zeitpunkt des Einschalten
			                if(@IPS_GetVariableIDByName('Startzeit',$parent)) {
			                    SetValue(@IPS_GetVariableIDByName('Startzeit',$parent),time());
			                    echo "Gestartet";
			                }
			                if(@IPS_GetVariableIDByName('Betriebszeit',$parent)) {
			                    SetValue(@IPS_GetVariableIDByName('Betriebszeit',$parent),0);
			                }
			            } else {
			                if(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent)) {
			                    // Übermitteln wenn dieser Zählerstand mindestens 50wh größer als letzter Zählerstand
			                    if(GetValue(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent)) < $_IPS["VALUE"] - 50) {
			                        $event = $_IPS["VALUE"] - GetValue(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent));
			                        SetValue(@IPS_GetVariableIDByName('Letzter Zählerstand',$parent),$_IPS["VALUE"]);
			                    }
			                } else {
			                    $i = IPS_CreateVariable(1);
			                    IPS_SetParent($i,$parent);
			                    IPS_SetName($i,'Letzter Zählerstand');
			                    IPS_SetVariableCustomProfile($i,"Watt-Stunden");
			                    SetValue($i,$_IPS['VALUE']);
			                }
			            }
			        }
			        if($event >0) {
			            SetValue(@IPS_GetVariableIDByName('Gesamtverbrauch',$parent),GetValue(@IPS_GetVariableIDByName('Gesamtverbrauch',$parent)) + $event);
			        }
			} // Handling of Variables

			// Minor Usage Treshhold - Wenn weniger als 10Wh Verbraucht werden, macht eine Übermittlung an die App keinen Sinn!
			if($event > 10) {
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
			            'Authorization: '.$authToken,
			            'Content-Type: application/json'
			        ),
			        CURLOPT_POSTFIELDS => json_encode($postData)
			    ));

			    $response = curl_exec($ch);

			    $responseData = json_decode($response, TRUE);
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
						$eid = IPS_CreateEvent(0);
						IPS_SetEventTrigger($eid, 1, $this->ReadPropertyInteger("meteringvariable"));
						IPS_SetParent($eid,  $this->ReadPropertyInteger("meteringvariable"));
						IPS_SetEventActive($eid, true);
						IPS_SetEventScript($eid, "corrently_update(".$this->InstanceID.");");
						IPS_SetName($eid, "Trigger Corrently App Event Update");
			}
		}

	}
