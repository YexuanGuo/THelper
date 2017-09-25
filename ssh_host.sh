#!/bin/bash
cat <<MENU
    1   =>  47.93.90.83 | 阿里云博客ECS
-----请输入ID-----
MENU
    echo -n "EnterHostId:"
    read host
    case "$host" in
        1|10.101.81.238)
            exec /usr/bin/sshpass -p aaaa.  ssh root@47.93.90.83 -p22
            ;;
        *)
        echo "Error, No host"
        ;;
esac