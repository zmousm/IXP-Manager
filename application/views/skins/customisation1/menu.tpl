
<table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td class="menubackgr" style="padding-left:5px;">
        <div id="myMenuID"></div>
        <script language="JavaScript" type="text/javascript" charset="t" defer="u">
            var myMenu =
            [
                {if $identity.user.privs eq 3}
                    [null,'Home','{genUrl controller="customer"}',null,'Home'],
                {elseif $identity.user.privs eq 2}
                    [null,'User Admin','{genUrl controller="cust-admin"}',null,'User Admin'],
                {elseif $identity.user.privs eq 1}
                    [null,'Dashboard','{genUrl controller="dashboard"}',null,'Dashboard'],
                {/if}
                _cmSplit,
                {if $identity.user.privs eq 3}
                    [null,'Super User',null,null,'Super User',
                    ['<img src="{genUrl}/images/joomla-admin/menu/globe4.png" />',     'Locations',            '{genUrl controller="location"}',
                                    null,'Locations' ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/cabinets.png" />',   'Cabinets',             '{genUrl controller="cabinet"}',
                                    null,'Cabinets'  ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/switch.png" />',     'Switches',             '{genUrl controller="switch"}',
                                    null,'Switches',
                        ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />',  'Switch Ports',  '{genUrl controller="switch-port"}',
                            null,'Switch Ports'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />',  'Add Ports',  '{genUrl controller="switch" action="add-ports"}',
    		                null,'Add Ports'   ]
                    ],
                    
					['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'IP Addresses', null, null, 'IP Addresses',

                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Add IP Addresses...',
                      	    '{genUrl controller='ipv4-address' action='add-addresses'}', null, 'Add IP Addresses...'
   					    ],

   						['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />','IPv4 Addresses',  
    						'{genUrl controller='ipv4-address' action='list'}', null, 'IPv4  Addresses'
						],
						['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />','IPv6 Addresses',  
							'{genUrl controller='ipv6-address' action='list'}', null, 'IPv6  Addresses'
						]
					],

                    ['<img src="{genUrl}/images/joomla-admin/menu/vendors.png" />',    'Vendors',              '{genUrl controller="vendor"}',
                                    null,'Vendors'   ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/vlan.png" />',       'VLANs',                '{genUrl controller="vlan"}',
                                    null, 'VLANs'   ]
                    ],
                    _cmSplit,
                    [null,'Admin',null,null,'Admin',
	                    ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','Members',  '{genUrl controller="customer"}',
	                                    null,'Members'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />', 'Interfaces', '{genUrl controller="virtual-interface"}', null, 'Interfaces',

		                        ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','Quick Add...',  '{genUrl controller="vlan-interface" action="quick-add"}',
                                    null, 'Quick Add...'   ],
	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','Physical Interfaces',  '{genUrl controller="physical-interface"}',
	                                        null, 'Physical Interfaces'   ],
	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','Virtual Interfaces',  '{genUrl controller="virtual-interface"}',
	                                        null, 'Virtual Interfaces'   ],
	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','VLAN Interfaces',  '{genUrl controller="vlan-interface"}',
	                                        null, 'VLAN Interfaces'   ]
	                    ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/system-users.png" />','Users',  '{genUrl controller="user"}',
	                                    null, 'Users'   ],

	                    ['<img src="{genUrl}/images/joomla-admin/menu/contents.png" />','Change Log',  '{genUrl controller="change-log"}',
	                                    null, 'Change Log'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/config.png" />','IRRDB Config',  '{genUrl controller="irrdb-config"}',
	                                    null, 'IRRDB Config'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/config.png" />', 'Utils', null, null, 'Utils',
	                        ['<img src="{genUrl}/images/joomla-admin/menu/php.png" />','PHP Info',  '{genUrl controller="utils" action="phpinfo"}',
	                            null, 'PHP Info'   ],
	                        ['<img src="{genUrl}/images/joomla-admin/menu/php.png" />','APC Info',  '{genUrl controller="utils" action="apcinfo"}',
	                            null, 'APC Info'   ]
	                    ]
                    ],
                    _cmSplit,
                {/if}
                {if $identity.user.privs neq 2}
                    [null,'Member Information',null,null,'Member Information',
                        ['<img src="{genUrl}/images/joomla-admin/menu/switch.png" />','Switch Configuration','{genUrl controller="dashboard" action="switch-configuration"}',null,'Switch Configuration'],
                        ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','Member Details','{genUrl controller="dashboard" action="members-details-list"}',null,'Member Details'],
                    ],
                    _cmSplit,
                {/if}
                {if $identity.user.privs eq 1 or $identity.user.privs eq 3}
                    [null,'Documentation',null,null,'Documentation',
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Technical Support',
                            '{genUrl controller="dashboard" action="static" page="support"}',
                            null, 'Technical Support'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Route Servers',
                            '{genUrl controller="dashboard" action="rs-info"}',
                            null, 'Route Servers'
                        ]
                    ],
                {/if}
                {if $identity.user.privs eq 3}
                    [null,'Statistics','{genUrl controller="customer" action="statistics-overview"}',null,'Statistics',
                        ['<img src="{genUrl}/images/joomla-admin/menu/system-users.png" />', 'Last Logins',  '{genUrl controller="user" action="last"}',
                            null, 'Last Logins'   ]
                    ],
                {/if}
                _cmSplit,
                {if $identity.user.privs eq 1}
                    [null, 'Support','{genUrl controller="dashboard" action="static" page="support"}',null,'Support'],
                    _cmSplit,
                {/if}
                {if $identity.user.privs neq 1}
                    [null,'Profile','{genUrl controller="profile"}',null,'Profile'],
                {else}
                    [null,'Profile','{genUrl controller="profile"}',null,'Profile',
                        ['<img src="{genUrl}/images/joomla-admin/menu/controlpanel.png" />', 'SEC Event Notifications',  '{genUrl controller="dashboard" action="sec-event-email-config"}',
                            null, 'SEC Event Notifications'   ]
                    ],
                {/if}
                {if $identity.user.privs eq 3}
                	_cmSplit,
                    [null, 'Help','{genUrl controller="index" action="help"}',null,'Help'],
                {/if}
            	_cmSplit,
                [null, 'About','{genUrl controller="index" action="about"}',null,'About'],
                _cmSplit,
                {if isset( $session->switched_user_from ) and $session->switched_user_from}
                    [null,'[Switch Back]','{genUrl controller="auth" action="switch-back"}',null,'[Switch Back]']
                {else}
                    [null,'[Logout]','{genUrl controller="auth" action="logout"}',null,'[Logout]']
                {/if}
            ];
            cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
        </script>
    </td>
</tr>
</table>

<div id="bd">

<br />

