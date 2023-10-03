#!/bin/bash
git checkout remote-src
git pull
git subtree pull --squash --prefix=src git@bitbucket.org:blancoservices/airs2020.git btr-tracked
git push

# create new branch for pull request
git branch -D remote-src-merge-request
git push origin --delete remote-src-merge-request
git checkout -b remote-src-merge-request
git push --set-upstream origin remote-src-merge-request

# merge with develop (disabled)
# git checkout develop
# git pull
# git merge --no-ff -m "Merge branch 'remote-src' into 'develop': update src dir" remote-src
