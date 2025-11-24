# 🗑️ Deleted Original Files

This document lists all the original TBDev files that were deleted during cleanup.

## Deleted PHP Files (Root Directory)

All old PHP files from the root directory were deleted:
- `admin.php`
- `announce.php`
- `bitbucket-upload.php`
- `browse.php`
- `chat.php`
- `comment.php`
- `confirm.php`
- `confirmemail.php`
- `delete.php`
- `deletemessage.php`
- `details.php`
- `donate.php`
- `download.php`
- `edit.php`
- `email-gateway.php`
- `faq.php`
- `filelist.php`
- `formats.php`
- `forums.php`
- `friends.php`
- `index.php` (old)
- `links.php`
- `login.php`
- `logout.php`
- `makepoll.php`
- `messages.php`
- `modtask.php`
- `my.php`
- `mytorrents.php`
- `ok.php`
- `peerlist.php`
- `polls.php`
- `queue-worker.php` (old)
- `recover.php`
- `redir.php`
- `reputation.php`
- `reputation_ad.php`
- `reputation_settings.php`
- `rules.php`
- `scrape.php` (old)
- `search.php`
- `sendmessage.php`
- `signup.php`
- `smilies.php`
- `staff.php`
- `tags.php`
- `take.php`
- `takeedit.php`
- `takefilesearch.php`
- `takelogin.php`
- `takemessage.php`
- `takeprofedit.php`
- `takerate.php`
- `takesignup.php`
- `takeupload.php`
- `topten.php`
- `upload.php`
- `useragreement.php`
- `userdetails.php`
- `userhistory.php`
- `users.php`
- `videoformats.php`
- `viewnfo.php`

## Deleted Directories

- `include/` - Old include files (replaced by `src/Core/`)
- `admin/` - Old admin files (replaced by `src/Controllers/Admin/`)
- `forums/` - Old forum files (replaced by `src/Controllers/Web/ForumController.php`)
- `bitbucket/` - Old bitbucket upload directory
- `captcha/` - Old captcha system
- `javairc/` - Old Java IRC applet (deprecated)
- `nbproject/` - NetBeans project files
- `scripts/` - Old JavaScript files
- `lang/` - Old language files

## Deleted Other Files

- `1.css` - Old stylesheet
- `2.css` - Old stylesheet
- `CHANGELOG.txt` - Old changelog
- `LICENSE.txt` - Old license (GPL license is in repository)

## ⚠️ Note About Images

The `pic/` directory was **NOT deleted** as it contains images that may still be referenced. You may want to:
1. Review which images are actually used
2. Migrate needed images to `public/images/`
3. Delete unused images later

## ✅ What Remains

All modernized files remain:
- `src/` - All new source code
- `views/` - All new templates
- `routes/` - Route definitions
- `public/` - Public web root
- `SQL/` - Database schemas
- Configuration files (composer.json, package.json, etc.)
- Documentation files

The application now uses **ONLY** the modernized files in `src/`, `views/`, `routes/`, and `public/`.

