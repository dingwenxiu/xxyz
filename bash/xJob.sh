#/bin/bash
cd /data/www/lottery/lottery_source/app

scp -r Jobs code-encryption:/data/lottery_source/

ssh -i /home/tom/.ssh/code-encryption.pem ec2-user@3.115.71.105  "/data/xJob.sh"

scp code-encryption:/data/lottery_encryption/job.tar.gz /data/www/xCode/
