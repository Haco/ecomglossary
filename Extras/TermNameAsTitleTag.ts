# EXT:ecomglossary
# Add Term title as page title
# Include this on the page where the ecomglossary plugin was included
config.noPageTitle = 2
temp.glossaryTitle = COA
temp.glossaryTitle {
    wrap = <title>|</title>
    5 = TEXT
    5.field = title
    5.wrap = |

    10 = RECORDS
    10 {
        dontCheckPid = 1
        tables = tx_ecomglossary_domain_model_term
        source.data = GP:tx_ecomglossary_ecomglossary|term
        source.intval = 1
        conf.tx_ecomglossary_domain_model_term = TEXT
        conf.tx_ecomglossary_domain_model_term {
            field = title
            htmlSpecialChars = 1
            stdWrap.noTrimWrap = |: ||
            stdWrap.required = 1
        }
    }
}

# Delete default title (ecom) & add Term Title
page.headerData.1391075689 >
page.headerData.323 < temp.glossaryTitle