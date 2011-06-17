{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Switch">
        Route Server Details
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>

<h3>Overview</h3>

<p>
Normally on a peering exchange, all connected parties will establish bilateral peering relationships
with each other member port connected to the exchange. As the number of connected parties increases,
it becomes increasingly more difficult to manage peering relationships with members of the exchange.
A typical peering exchange full-mesh eBGP configuration might look something similar to the diagram
on the left hand side.
</p>

<table border="0" align="center">
<tr>
    <td width="354">
        <img src="{genUrl}/images/route-server-peering-fullmesh.png" title=" IXP full mesh peering relationships" alt="[ IXP full mesh peering relationships ]" width="345" height="317" />
    </td>
    <td width="25"></td>
    <td width="354">
        <img src="{genUrl}/images/route-server-peering-rsonly.png" title=" IXP route server peering relationships" alt="[  IXP route server peering relationships ]" width="345" height="317" />
    </td>
</tr>
<tr>
    <td align="center">
        <em> IXP full mesh peering relationships </em>
    </td>
    <td></td>
    <td align="center">
        <em> IXP route server peering relationships</em>
    </td>
</tr>
</table>

<p>
<br />
The full-mesh BGP session relationship scenario requires that each BGP speaker configure and manage
BGP sessions to every other BGP speaker on the exchange. In this example, a full-mesh setup requires
7 BGP sessions per member router, and this increases every time a new member connects to the exchange.
</p>

<p>
However, by using a route server for all peering relationships, the number of BGP sessions per router
stays at two: one for each route server. Clearly this is a more sustainable way of maintaining IXP
peering relationships with a large number of participants.
</p>


<h3>Should I use this service?</h3>

<p>
The route server cluster is aimed at:
</p>

<ul>
    <li> small to medium sized members of the exchange who don't have the time or resources to
         aggressively manage their peering relationships
    </li>
    <li> larger members of the exchange who have an open peering policy, but where it may not
         be worth their while managing peering relationships with smaller members of the exchange.
    </li>
</ul>

<p>
As a rule of thumb: <strong>If you don't have any good reasons not to use the route server cluster, you should probably use it.</strong>
</p>

<p>
The service is designed to be reliable. It operates on two physical servers, each located in a
different data centre. The service is available on both ipv4 and ipv6. Each server runs a separate 
routing daemon . Should a single BGP server die for some unlikely reason, no other BGP
server is likely to be affected. If one of the physical servers becomes unavailable, the second server
will continue to provide BGP connectivity.
</p>

<h3>How do I use the service?</h3>

<p>
If enabled, the route servers are set up to accept BGP connections from your router. Once this has
been done, you will need to configure a BGP peering session to the correct internet address. The
IP addresses of the route servers are listed as follows:
</p>


<p>
<br /><br />
For Cisco routers, you will need something like the following bgp configuration:
</p>

<pre>
    router bgp 99999
     no bgp enforce-first-as

     ! Route server #1

     neighbor 193.242.111.8 remote-as 43760
     neighbor 193.242.111.8 description INEX Route Server
     address-family ipv4
     neighbor 193.242.111.8 password s00persekr1t
     neighbor 193.242.111.8 activate
     neighbor 193.242.111.8 filter-list 100 out

     ! Route server #2

     neighbor 193.242.111.9 remote-as 43760
     neighbor 193.242.111.9 description INEX Route Server
     address-family ipv4
     neighbor 193.242.111.9 password s00persekr1t
     neighbor 193.242.111.9 activate
     neighbor 193.242.111.9 filter-list 100 out
</pre>

<p>
You should also use <code>route-maps</code> (or <code>distribute-lists</code>) to control
outgoing prefix announcements to allow only the prefixes which you indend to announce.
</p>



</div>
</div>

{tmplinclude file="footer.tpl"}
