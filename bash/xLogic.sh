#/bin/bash
cd /data/www/lottery/lottery_source/app/Lib

scp -r Logic code-encryption:/data/lottery_source/

ssh -i /home/tom/.ssh/code-encryption.pem ec2-user@3.115.71.105  "/data/xLogic.sh"

scp code-encryption:/data/lottery_encryption/lib_logic.tar.gz /data/www/xCode/
