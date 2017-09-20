.PHONY: all version2 version3

# general tools
HIDECMD      = @
CP           = cp
MKDIR        = mkdir
RM           = rm
CD           = cd
ZIP          = zip
UNZIP        = unzip
WGET         = wget
ECHO         = $(HIDECMD) echo
EMPTYLINE    = $(HIDECMD) echo -e
WAITFORENTER = $(HIDECMD) read -p "Press ENTER to continue..."

# flags
CPFLAGS    = -r
MKDIRFLAGS = -p
RMFLAGS    = -rf
ZIPFLAGS   = -r

# directories
MOODLE2 = moodle2
MOODLE3 = moodle2.9_and_3+
INSTALL = install
TEMP    = _TEMP_
JSX     = jsxgraph

# zip file name
MOODLE2ZIP = install_jsxgraph_plugin_moodle2.zip
MOODLE3ZIP = install_jsxgraph_plugin_moodle3.zip

# core location
SERVERCORE = http://jsxgraph.uni-bayreuth.de/distrib/jsxgraphcore.js
LOCALCORE  = ../jsxgraph/build/jsxgraphcore.min.js
CORE   = jsxgraphcore.min.js

local: update_jsxgraphcore_from_local version2 version3

server: update_jsxgraphcore_from_server version2 version3

update_jsxgraphcore_from_server:
	$(EMPTYLINE)
	$(ECHO) UPDATING JSXGRAPH-CORE FROM SERVER
	$(ECHO) "##################################"

	$(EMPTYLINE)
	$(RM) $(RMFLAGS) $(TEMP)/$(CORE)
	$(WGET) $(SERVERCORE) -O $(TEMP)/$(CORE)
	$(CP) $(CPFLAGS) $(TEMP)/$(CORE) $(MOODLE2)
	$(CP) $(CPFLAGS) $(TEMP)/$(CORE) $(MOODLE3)
	$(RM) $(RMFLAGS) $(TEMP)/$(CORE)

	$(EMPTYLINE)
	$(ECHO) complete...
	$(EMPTYLINE)

update_jsxgraphcore_from_local: $(LOCALCORE)
	$(EMPTYLINE)
	$(ECHO) UPDATING JSXGRAPH-CORE FROM LOCAL SOURCE
	$(ECHO) "########################################"

	$(EMPTYLINE)
	$(RM) $(RMFLAGS) $(TEMP)/$(CORE)
	$(CP) $(CPFLAGS) $(LOCALCORE) $(TEMP)
	$(CP) $(CPFLAGS) $(TEMP)/$(CORE) $(MOODLE2)
	$(CP) $(CPFLAGS) $(TEMP)/$(CORE) $(MOODLE3)
	$(RM) $(RMFLAGS) $(TEMP)/$(CORE)

	$(EMPTYLINE)
	$(ECHO) complete...
	$(EMPTYLINE)

version2: $(MOODLE2)
	$(EMPTYLINE)
	$(ECHO) UPDATING MOODLE2 INSTALL-ZIP
	$(ECHO) "############################"

	$(EMPTYLINE)
	$(ECHO) Removing old files
	$(ECHO) "------------------"
	$(RM) $(RMFLAGS) $(MOODLE2)/$(INSTALL)/$(MOODLE2ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(MOODLE2ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Copying new files in a temporary folder
	$(ECHO) "---------------------------------------"
	$(MKDIR) $(MKDIRFLAGS) $(TEMP)/$(JSX)
	$(CP) $(CPFLAGS) $(MOODLE2)/* $(TEMP)/$(JSX)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)/$(INSTALL)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Zipping
	$(ECHO) "-------"
	$(CD) $(TEMP); \
	$(ZIP) $(ZIPFLAGS) $(MOODLE2ZIP) $(JSX)/*
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Copying new zip and clear temporary folder
	$(ECHO) "------------------------------------------"
	$(CP) $(CPFLAGS) $(TEMP)/$(MOODLE2ZIP) $(MOODLE2)/$(INSTALL)/
	$(RM) $(RMFLAGS) $(TEMP)/$(MOODLE2ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) complete...
	$(EMPTYLINE)

version3: $(MOODLE3)
	$(EMPTYLINE)
	$(ECHO) UPDATING MOODLE3 INSTALL-ZIP
	$(ECHO) "############################"

	$(EMPTYLINE)
	$(ECHO) Removing old files
	$(ECHO) "------------------"
	$(RM) $(RMFLAGS) $(MOODLE3)/$(INSTALL)/$(MOODLE3ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(MOODLE3ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Copying new files in a temporary folder
	$(ECHO) "---------------------------------------"
	$(MKDIR) $(MKDIRFLAGS) $(TEMP)/$(JSX)
	$(CP) $(CPFLAGS) $(MOODLE3)/* $(TEMP)/$(JSX)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)/$(INSTALL)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Zipping
	$(ECHO) "-------"
	$(CD) $(TEMP); \
	$(ZIP) $(ZIPFLAGS) $(MOODLE3ZIP) $(JSX)/*
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Copying new zip and clear temporary folder
	$(ECHO) "------------------------------------------"
	$(CP) $(CPFLAGS) $(TEMP)/$(MOODLE3ZIP) $(MOODLE3)/$(INSTALL)/
	$(RM) $(RMFLAGS) $(TEMP)/$(MOODLE3ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) complete...
	$(EMPTYLINE)