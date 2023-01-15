files="/data/acte_sous_signature_privee/src/assets/documents/*/"
for entry in  $files ; do

if [ -z "$(ls -A $entry)" ]; then
   rm -R "$entry" ;
   echo "$entry removed"
else
   echo "Not Empty"
fi
done