# EXT:ecomglossary
# Add Term title as page title
temp.glossaryTitle = COA
temp.glossaryTitle {
    wrap = <title>|</title>
    5 = TEXT
    5.field = subtitle
    5.override = Definition: Simply explained in the Ex-Glossary | ecom instruments
    5.override.if.isFalse.field = subtitle
    5.wrap = |

    2 = RECORDS
    2 {
        dontCheckPid = 1
        tables = tx_ecomglossary_domain_model_term
        source.data = GP:tx_ecomglossary_ecomglossary|term
        source.intval = 1
        conf.tx_ecomglossary_domain_model_term = TEXT
        conf.tx_ecomglossary_domain_model_term {
            field = title
            override.field = meta_title
            override.if.isTrue.field = meta_title
            htmlSpecialChars = 1
            stdWrap.noTrimWrap = || |
            stdWrap.required = 1
        }
    }
}

temp.glossaryMetaDescription = COA
temp.glossaryMetaDescription {
    2 = RECORDS
    2 {
        dontCheckPid = 1
        tables = tx_ecomglossary_domain_model_term
        source.data = GP:tx_ecomglossary_ecomglossary|term
        source.intval = 1
        conf.tx_ecomglossary_domain_model_term = TEXT
        conf.tx_ecomglossary_domain_model_term {
            field = meta_description
            htmlSpecialChars = 1
            stdWrap.noTrimWrap = |||
            stdWrap.required = 1
        }
    }
}

[globalVar = GP:tx_ecomglossary_ecomglossary|term > 0]
    # Delete default title (ecom) & add Term Title
    config.noPageTitle = 2
    page.headerData.1391075689 >
    page.headerData.323 < temp.glossaryTitle

    # Description if meta available
    page.meta.description.override.stdWrap.cObject < temp.glossaryMetaDescription
    page.meta.description.override.if.isTrue = temp.glossaryMetaDescription
[global]