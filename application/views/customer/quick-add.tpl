{tmplinclude file="header.tpl"}

<div class="content">

<h2>Customer :: Quick Add</h2>

{$form}

</div>

{literal}
<script type="text/javascript"> /* <![CDATA[ */ 

$(document).ready( function() {
	
    $( '#ipv6address' ).bind( 'focus', function() {
    	
        asn  = jQuery.trim( $( '#autsys' ).val() );
        v6   = jQuery.trim( $( '#ipv6address' ).val() );

        ipv6_start = '2001:7f8:1c:3000::';
		ipv6_end   = ':1';
        
        if( asn != '' && v6 == '' )
        {
            if( asn.length > 4 )
            {
                b = asn.substring( 0, asn.length - 4 ) + ':';
                a = asn.substring( asn.length - 4 );
            }
            else
            {
                b = '';
                a = asn;
            }
            
			$( '#ipv6address' ).val( ipv6_start + b + a + ipv6_end );
        }
    });
});

/* ]]> */ </script> 
{/literal}



{tmplinclude file="footer.tpl"}

