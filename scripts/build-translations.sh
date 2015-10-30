xgettext *.php lib/*.php --foreign-user --output=locale/messages.pot --msgid-bugs-address=shylux@gmail.com
msgmerge --update locale/de_CH/LC_MESSAGES/messages.po locale/messages.pot --force-po
msgfmt locale/de_CH/LC_MESSAGES/messages.po --output-file locale/de_CH/LC_MESSAGES/messages.mo
