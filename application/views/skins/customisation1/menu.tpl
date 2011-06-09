
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
                            null,'Switch Ports'   ]
                    ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/vendors.png" />',    'Vendors',              '{genUrl controller="vendor"}',
                                    null,'Vendors'   ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/console.png" />',    'Console Connections',  '{genUrl controller="console-server-connection"}',
                                    null,'Console Connections'   ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/vlan.png" />',       'VLANs',                '{genUrl controller="vlan"}',
                                    null, 'VLANs'   ]
                    ],
                    _cmSplit,
                    [null,'Admin',null,null,'Admin',
	                    ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','Members',  '{genUrl controller="customer"}',
	                                    null,'Members'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />', 'Interfaces', '{genUrl controller="virtual-interface"}', null, 'Interfaces',

	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','Physical Interfaces',  '{genUrl controller="physical-interface"}',
	                                        null, 'Physical Interfaces'   ],
	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','Virtual Interfaces',  '{genUrl controller="virtual-interface"}',
	                                        null, 'Virtual Interfaces'   ],
	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','VLAN Interfaces',  '{genUrl controller="vlan-interface"}',
	                                        null, 'VLAN Interfaces'   ]

	                    ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/kontact.png" />','Contacts',  '{genUrl controller="contact"}',
	                                    null, 'Contacts'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/system-users.png" />','Users',  '{genUrl controller="user"}',
	                                    null, 'Users'   ],

	                    ['<img src="{genUrl}/images/joomla-admin/menu/drive-optical.png" />','Customer Kit',  '{genUrl controller="cust-kit"}',
	                                    null, 'Customer Kit'   ],
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
                {if $identity.user.privs neq 2}
                    [null,'Peering',null,null,'Peering',
                        ['<img src="{genUrl}/images/joomla-admin/menu/joomla_16x16.png" />','Peering Matrices', null, null, 'Peering Matrices',

                            {foreach from=$config.peering_matrix.public key=index item=lan}
                                ['<img src="{genUrl}/images/joomla-admin/menu/joomla_16x16.png" />',
                                    '{$lan.name}', '{genUrl controller="dashboard" action="peering-matrix" lan=$index}',
                                    'ixp_new_window',
                                    '{$lan.name}'
                                ],
                            {/foreach}

                        ],
                        {if $identity.user.privs eq 1 and $customer->isFullMember()}
                            ['<img src="{genUrl}/images/joomla-admin/menu/joomla_16x16.png" />',
                                'My Peering Manager','{genUrl controller="dashboard" action="my-peering-matrix"}',
                                null, 'My Peering Manager'
                            ],
                            {/if}
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
                            null, 'Last Logins' ]
                    ],
	                _cmSplit,
	            {/if}
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

