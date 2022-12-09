import {ReduxModule} from '../../../abstract/reduxModule'

class ThemeForm extends ReduxModule {

  getNamespace () {
    return '[Theme form]'
  }

  getInitialState () {
    return {}
  }
}

export default ThemeForm

/*export class ThemeForm extends abstractModule {
  constructor () {
    super();
    this.initNamespace('THEMES_');
  }

  initInitialState () {
    const rgbaString = 'rgba(0, 0, 0, 1)';
    const string = '';
    const int = 0;
    const boolean = false;
    const languageCode = 'en';
    const File = new File;

    const FontOptions = {
      family: string,
      size: int,
      style: {
        bold: boolean,
        italic: boolean,
        underline: boolean
      }
    };

    // sample of structure for backend
    const BackendThemeOptions = {
      type: 'enhanced' || 'plain',
      summary: string,
      conclusion: string,
      header: {
        imageUrl: string,
        logoLink: string || '',
        title: 'Newsletter' // only for enhanced
      },
      fonts: {
        header: FontOptions,
        tableOfContents: FontOptions,
        feeTitle: FontOptions,
        articleHeadline: FontOptions,
        source: FontOptions,
        author: FontOptions,
        date: FontOptions,
        articleContent: FontOptions
      },
      content: {
        highlightKeywords: {
          bold: boolean,
          color: rgbaString
        },
        showInfo: {
          sourceCountry: boolean,
          articleSentiment: boolean,
          articleCount: boolean,
          images: boolean,
          sharingOptions: boolean,
          sectionDivider: boolean,
          userComments: 'no' || 'with_author_date' || 'without_author_date',
          tableOfContents: {
            visible: boolean,
            headline: 'no' || 'headline' || 'headline_source_date' || 'source_headline_date'
          }
        },
        language: languageCode,
        extract: 'start' || 'context' || 'no'
      },
      colors: {
        background: {
          header: rgbaString,
          emailBody: rgbaString,
          accent: rgbaString
        },
        text: {
          header: rgbaString,
          articleHeadline: rgbaString,
          articleContent: rgbaString,
          author: rgbaString,
          publishDate: rgbaString,
          source: rgbaString
        }
      }
    };

    this.initialState = fromJS({
      name: string,
      template: string,
      options: {
        type: 'enhanced' || 'plain',
        summary: {
          value: string,
          isEdit: boolean
        },
        conclusion: {
          value: string,
          isEdit: boolean
        },
        header: {
          imageUrl: {
            file: File,
            url: string
          },
          logoLink: string,
          title: {
            value: string,
            isEdit: boolean
          }
        },
        fonts: {
          header: FontOptions,
          tableOfContents: FontOptions,
          feeTitle: FontOptions,
          articleHeadline: FontOptions,
          source: FontOptions,
          author: FontOptions,
          date: FontOptions,
          articleContent: FontOptions
        },
        content: {
          language: languageCode,
          extract: {
            value: 'start',
            entities: [{
              value: 'start',
              label: 'Start of text extract'
            }, {
              value: 'context',
              label: 'Contextual extract'
            }, {
              value: 'no',
              label: 'No article extract'
            }]
          },
          highlightKeywords: {
            bold: boolean,
            color: rgbaString,
            colorPresets: [rgbaString]
          },
          showInfo: {
            sourceCountry: boolean,
            articleSentiment: boolean,
            articleCount: boolean,
            images: boolean,
            sharingOptions: boolean,
            sectionDivider: boolean,
            userComments: {
              value: 'no',
              entities: [{
                value: 'no',
                label: 'No User Comments'
              }, {
                value: 'with_author_date',
                label: 'User Comments with Author/Date'
              }, {
                value: 'without_author_date',
                label: 'User Comments without Author/Date'
              }]
            },
            tableOfContents: {
              types: {
                value: 'simple',
                entities: [{
                  value: 'simple',
                  label: 'Table of contents'
                }, {
                  value: 'headlines',
                  label: 'Table of contents with headlines'
                }, {
                  value: 'no',
                  label: 'No table of contents'
                }]
              },
              headlines: {
                value: null,
                entities: [{
                  value: 'headline',
                  label: 'Headline only'
                }, {
                  value: 'headline_source_date',
                  label: 'Headline | Source | Date'
                }, {
                  value: 'source_headline_date',
                  label: 'Source | Headline | Date'
                }]
              }
            }

          }
        },
        colors: {
          background: {
            header: rgbaString,
            emailBody: rgbaString,
            accent: rgbaString
          },
          text: {
            header: rgbaString,
            articleHeadline: rgbaString,
            articleContent: rgbaString,
            author: rgbaString,
            publishDate: rgbaString,
            source: rgbaString
          }
        }
      }
    });
  }

  initActions () {
    this.actions = {
    };
  }
}

const themeForm = new ThemeForm();
themeForm.init();

export default themeForm.reducers;
*/
