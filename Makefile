.PHONY: update_jsxgraphcore_from_local update_jsxgraphcore_from_server version2

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

# directories and files
INSTALL  = install
TEMP     = _TEMP_
JSX      = jsxgraph
FILES    = filter.php filtersettings.php geonext.min.js $(CORE) styles.css version.php lang/* $(README)
README   = README.md
ZIPPED   = install_jsxgraph_plugin_moodle2.zip

# core location
SERVERCORE = http://jsxgraph.uni-bayreuth.de/distrib/jsxgraphcore.js
LOCALCORE  = ../jsxgraph/build/jsxgraphcore.min.js
CORE       = jsxgraphcore.js

local: update_jsxgraphcore_from_local version2

server: update_jsxgraphcore_from_server version2

update_jsxgraphcore_from_server:
	$(EMPTYLINE)
	$(ECHO) UPDATING JSXGRAPH-CORE FROM SERVER
	$(ECHO) "##################################"

	$(EMPTYLINE)
	$(RM) $(RMFLAGS) $(CORE)
	$(WGET) $(SERVERCORE) -O $(CORE)

	$(EMPTYLINE)
	$(ECHO) complete...
	$(EMPTYLINE)

update_jsxgraphcore_from_local: $(LOCALCORE)
	$(EMPTYLINE)
	$(ECHO) UPDATING JSXGRAPH-CORE FROM LOCAL SOURCE
	$(ECHO) "########################################"

	$(EMPTYLINE)
	$(RM) $(RMFLAGS) $(CORE)
	$(CP) $(CPFLAGS) $(LOCALCORE) $(CORE)

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
	$(RM) $(RMFLAGS) $(INSTALL)/$(ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(ZIP)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Copying new files in a temporary folder
	$(ECHO) "---------------------------------------"
	$(MKDIR) $(MKDIRFLAGS) $(TEMP)/$(JSX)
	$(foreach f,$(FILES),$(CP) $(CPFLAGS) $f $(TEMP)/$(JSX);)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Zipping
	$(ECHO) "-------"
	$(CD) $(TEMP); \
	$(ZIP) $(ZIPFLAGS) $(ZIPPED) $(JSX)/*
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) Copying new zip and clear temporary folder
	$(ECHO) "------------------------------------------"
	$(CP) $(CPFLAGS) $(TEMP)/$(ZIPPED) $(INSTALL)
	$(RM) $(RMFLAGS) $(TEMP)/$(ZIPPED)
	$(RM) $(RMFLAGS) $(TEMP)/$(JSX)
	$(RM) $(RMFLAGS) $(TEMP)
	$(EMPTYLINE)

	$(EMPTYLINE)
	$(ECHO) complete...
	$(EMPTYLINE)
