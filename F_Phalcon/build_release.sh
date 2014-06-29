#/bin/bash
# build release code by original folder
#@author: dogstar 20140127

CUR_PATH="${PWD}"

mkdir -p ${CUR_PATH}/release
rm ${CUR_PATH}/release/* -rf

echo "[START TO BUILDING RELEASE ...]"
echo ''

cd ${CUR_PATH}/original/
find -name "*.php" | while read line
do
    echo "[BUILDING...] "$line

    filename=$(basename $line)
    foldername=$(dirname $line)
    if [ "$foldername" = "." ]
    then
        foldername=''
    fi
    mkdir -p ${CUR_PATH}/release/${foldername}

    cat $line | grep -v "\[TEST TRACE\]" > ${CUR_PATH}/release/${foldername}/${filename}
done

echo ''
echo "[FINISHI TO BUILD RELEASSE!]"
