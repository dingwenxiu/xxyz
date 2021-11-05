#/bin/bash
cd /data/www/lottery/lottery_source

scp  -r config code-encryption:/data/lottery_source/

ssh -i /home/tom/.ssh/code-encryption.pem ec2-user@3.115.71.105  "/data/xConfig.sh"

scp code-encryption:/data/lottery_encryption/config.tar.gz /data/www/xCode/
