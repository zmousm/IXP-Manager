#! /bin/sh


# Update public/js/900-oss-framework.js from OSS-Framework

DIR="$( cd "$( dirname "$0" )" && pwd )"
DEST="${DIR}/../public/js/900-oss-framework.js";
SOURCE="${DIR}/../library/OSS-Framework.git/data/js";


echo "/**
 * This file is a combination of other JavaScript files from the OSS-Framework.
 *
 * See: https://github.com/opensolutions/OSS-Framework/tree/master/data/js 
 *
 * NOTICE: Do not edit this file as it will be overwritten the next time you run
 *         the by update-oss-js.sh script referenced above.
 *
 * Copyright (c) 2013 Open Source Solutions Limited, Dublin, Ireland
 * All rights Reserved.
 *
 * http://www.opensolutions.ie/
 *
 * Author: Open Source Solutions Limited <info _at_ opensolutions.ie>
 *
 */
 
" > $DEST

 /bin/cat "$SOURCE/100-message.js" "$SOURCE/110-error.js" "$SOURCE/120-modal-dialog.js" >> $DEST
 /bin/cat "$SOURCE/130-toggle.js" "$SOURCE/140-tooltip.js" "$SOURCE/200-popover.js" >> $DEST
 /bin/cat "$SOURCE/300-chosen.js" "$SOURCE/320-throbber.js" "$SOURCE/400-utility.js" >> $DEST
 /bin/cat "$SOURCE/600-datatables.js" >> $DEST
