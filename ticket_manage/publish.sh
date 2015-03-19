#!/usr/bin/env bash

echo "Tag list:"
git tag
echo ""
echo "Please choose a tag to publish :"
read git_tag_val

echo ""
echo "Server list:"
web1="deploy@10.160.35.92:/srv/deploy/fx-backend"
echo "web1"
echo ""
echo "Please choose a server to publish :"
read target_server

echo ""
echo "================!!!CONFIRM!!!==============="
echo ""
read -p "Publish $git_tag_val to $target_server ? (y/n) "
if [[ $REPLY =~ ^[Yy]$ ]]; then

	mkdir /tmp/__master/

	git archive $git_tag_val | tar -x -C /tmp/__master/

	rsync -avzP \
		--exclude 'Uploads/*' \
		--exclude 'Logs/*' \
		--exclude 'Configs/Config.php' \
		--delete \
	/tmp/__master/ ${!target_server}

	rm -rf /tmp/__master/
fi
