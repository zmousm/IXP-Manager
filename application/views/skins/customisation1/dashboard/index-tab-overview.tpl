


            <h2>Recent Members</h2>

            <p>Our three most recent members are listed below. {if $customer->isFullMember()}Have you arranged peering with them yet?{/if}</p>

            <div id="recentMembersContainer">
                <table id="recentMembersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>AS Number</th>
                            <th>Date Joined</th>
                            {if $customer->isFullMember()}
                                <th>Peering Contact</th>
                            {/if}
                        </tr>
                    </thead>
                    <tbody>

                    {foreach from=$recentMembers item=member}
                        <tr>
                            <td>{$member.name}</td>
                            <td>{$member.autsys|asnumber}</td>
                            <td>{$member.datejoin}</td>
                            {if $customer->isFullMember()}
                                <td><a href="{genUrl controller='dashboard' action='my-peering-matrix' email=$member.id}">{$member.peeringemail}</a></td>
                            {/if}
                        </tr>
                    {/foreach}

                    </tbody>
                </table>
            </div>

            <script type="text/javascript">
                {literal}
                var recentMembersDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "recentMembersTable" ) );
                recentMembersDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

                recentMembersDataSource.responseSchema = {
                    fields: [
                        {key:'Name'},
                        {key:'AS Number'},
                        {key:'Date Joined'},
                        {/literal}{if $customer->isFullMember()}{ldelim}key:'Peering Contact'{rdelim}{/if}{literal}
                    ]
                };

                var recentMembersColumnDefs = [
                    {key:'Name'},
                    {key:'AS Number'},
                    {key:'Date Joined'},
                    {/literal}{if $customer->isFullMember()}{ldelim}key:'Peering Contact'{rdelim}{/if}{literal}
                ];

                var recentMembersDataTable = new YAHOO.widget.DataTable( "recentMembersContainer", recentMembersColumnDefs, recentMembersDataSource );
                {/literal}
            </script>


