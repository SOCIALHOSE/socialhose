<?php

namespace CacheBundle\Document\Extractor;

use PHPUnit\Framework\TestCase;
use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Class BasicDocumentContentExtractorTest
 * @package CacheBundle\Document\Extractor
 */
class BasicDocumentContentExtractorTest extends TestCase
{

    const SIMPLE_TEXT = <<<EOT
Outside a character class, in the default matching mode, the circumflex character (^) is an assertion which is true only if the current matching point is at the start of the subject string. Inside a character class, circumflex (^) has an entirely different meaning (see below).

Circumflex (^) need not be the first character of the pattern if a number of alternatives are involved, but it should be the first thing in each alternative in which it appears if the pattern is ever to match that branch. If all possible alternatives start with a circumflex (^), that is, if the pattern is constrained to match only at the start of the subject, it is said to be an "anchored" pattern. (There are also other constructs that can cause a pattern to be anchored.)

A dollar character ($) is an assertion which is TRUE only if the current matching point is at the end of the subject string, or immediately before a newline character that is the last character in the string (by default). Dollar ($) need not be the last character of the pattern if a number of alternatives are involved, but it should be the last item in any branch in which it appears. Dollar has no special meaning in a character class.

The meaning of dollar can be changed so that it matches only at the very end of the string, by setting the PCRE_DOLLAR_ENDONLY option at compile or matching time. This does not affect the \Z assertion.

The meanings of the circumflex and dollar characters are changed if the PCRE_MULTILINE option is set. When this is the case, they match immediately after and immediately before an internal "\n" character, respectively, in addition to matching at the start and end of the subject string. For example, the pattern /^abc$/ matches the subject string "def\nabc" in multiline mode, but not otherwise. Consequently, patterns that are anchored in single line mode because all branches start with "^" are not anchored in multiline mode. The PCRE_DOLLAR_ENDONLY option is ignored if PCRE_MULTILINE is set.

Note that the sequences \A, \Z, and \z can be used to match the start and end of the subject in both modes, and if all branches of a pattern start with \A is it always anchored, whether PCRE_MULTILINE is set or not. Cat also been here.
EOT;

    const ARABIC_TEXT = <<<EOT
مع مدن يرتبط المؤلّفة, حين تونس تحرّكت في. بعض لعملة استعملت الخارجية مع, حلّت تعديل كثيرة دون إذ, شيء ما وأكثرها الأوروبي. ما أضف وبحلول الإتحاد والنفيس, أم الخطّة المشترك بالمحور بين. لهذه والحزب معاملة وتم و, أضف أي إبّان يتمكن المتحدة. بسبب مسارح بعد ثم, هناك إتفاقية دون كل. مرجع سكان الأمور ان ضرب.

التاريخ، بالمطالبة لمّ هو, إذ حدى العدّ المشترك الإيطالية. عرفها يعادل والكساد بحق ٣٠, في قامت ميناء أطراف حول. ذلك المزيفة ويكيبيديا، من, قد وبعدما العالم المتحدة حدى. كلّ تم حقول قدما مقاومة. الشتاء، اليابان عل وصل, انه بـ ٠٨٠٤ تحرّك مدينة. سابق أثره، كلّ ٣٠.

و للجزر السادس وفنلندا دون, مئات الخطّة الأبرياء من قبل, كل لان نهاية فهرست الإحتفاظ. و بحق الشتوية اليابانية, بوابة والفلبين الإمتعاض تم ولم, اتّجة الصعداء باستحداث عدد قد. ذات تم نهاية كانتا. بينما المسرح إذ أخر, بداية اقتصادية ما ومن. أخذ لعدم ويكيبيديا، في.

مع قِبل لإعادة الحدود بها. من شيء انتهت الصينية, أمدها وقامت مع بال, هو قدما احداث وقوعها، غير. قد مما مقاومة الأثنان, هو وحتى واستمر وفنلندا أخذ. ثم حول الجوي وبدأت.

في وقد اللا لأداء وتتحمّل, تم وفي واتّجه الخاسرة. ولاتّساع الانجليزية هو لها. أما أن تونس عقبت, في بلا اعلان قبضتهم. قد أهّل للإتحاد قام, هو لمّ أواخر لليابان. ان كان يذكر الوزراء.

لها بهيئة أعمال ديسمبر أم. أسر عقبت بالرّد ان. الجنوب السادس الشهيرة لم قام. مايو أعمال باستحداث وفي ان, أخر أطراف حاملات من. عل أحكم المشترك حتى, قد ببعض الثقيلة واعتلاء فعل.

للجزر المعاهدات أن تلك, أخر هو وبدأت بشرية والنفيس. تم على الجنود الإحتفاظ. ميناء العالمي لبولندا، ثم كما, وجهان للحكومة حدى في. كما أم تجهيز إختار. دنو جيما إعلان بـ, جهة ٣٠ فاتّبع موالية.

فقد أم يونيو لإعلان مساعدة, عدم ان الحيلولة الولايات. لم به، قررت وقدّموا الأراضي, دخول تاريخ بالسيطرة حدى من. ٣٠ الآخر وعُرفت واتّجه مكن, عن دار أمّا البرية, والقرى لمحاكم أن على. وقوعها، اليميني و تعد. لكل مع سقوط نتيجة, الشتاء
EOT;

    const HIEROGLYPHIC_TEXT = <<<EOT
絵列個列氏。さおひまきんせふこ二「夜等鵜もぬねゃ舳カ」ラュャコ。ほゅ。るたろの模他舳無るゆめぬへッウウほこお派譜もれふやモカホルーちあしゆゃひく他樹御以擢魔やセノきぬ離日みとゃゆしやねく津鵜ろ絵サハルウまはおこれと列。そ雲瀬手たとふえお。

夜手屋根離留二区くそまへャタ。シツコ個等派以露やと。派阿ヌヤハみのは無二っれすラヘリオゆるは、個保津れなのゆ差津阿樹舳屋模御擢擢瀬かそく、ょちとは。擢他舳差夜津離以よよてほちみっみたきらむき素鵜もおんほさけむちむとかもへねよい日へ課雲い津区まに。毛名区手ふねへみ鵜野屋すけたやのちこはよけら屋阿いきょ知野みれくオヘートヒ手津むすねひうつ根つらぬ舳魔。

やっ樹屋絵尾いとひすらろさめめっよふ阿ぬそ夜絵くは、差派留保個へそミヒケコそふららこほるめむひひ素、絵他いゃつうよほョケャヌョヌこねと瀬留尾野名夜無は尾オャエュ離擢う絵瀬氏素素雲都遊。

け等等ヤッえょ留保雲、けにねせえけまねひすうのちん保知おねやのきゃょしんうこよょエケそょいタスッタゅ雲氏クエ尾個な以鵜模手しっけ留ぬけっむ雲遊魔ん模樹都あまさとさ露阿都留離屋クヨツアおすよ以個れかゅりせくんゆ。の差個よる。とユシイてゃむ、ゆほよら個都個よやま。

阿ゃえ雲素鵜ウタャヤサ譜等譜模氏野列瀬列夜目る個樹夜つよっみ野名根絵擢鵜ツロリムタフキム。え阿二なたさもは露ね。ほに、露留とむ区くゃまん目阿ろて二御保露課屋よのっうな都とはゅ以樹野巣まむけカオリサむ。遊留野絵樹毛ちへ擢にねろれるれあこらねたゃま。ゃさゆろうゃカムマクヒケつとへけ模るてらかのえ。

ゅちくく、。ぬ、離あおへひ課保以等すほゆよ派尾樹ケコヨョるゃ保派クエコセ、ねまゅいひュメカ津二ホハヨスミ阿保雲屋舳他れ譜これめてん個等るしもょけまちるむめ毛氏派無離ゆかぬょふ課模っあせっこ巣あゆそもち。ひ、区絵せありきけユサネメ手個日擢目。

雲都はひか根鵜んまなミホチソ名個阿無津魔きりさもかへううもぬそすととか根根。留等るこすひ樹舳留差うすまっにムーユゆ無鵜いつゆくイツイスウヨユエソ、露派くむゅる樹野よけかほりホヘョ魔ひたん列毛、あこほし。野差保都舳。あぬ区留あ模名コヨン都列列りな等根ちんゃたゃすもいくあてオャサリかろ瀬模保野鵜名ういろゅ。た雲模、おかこ、ろろほ魔区離屋ゃしな。

列ち鵜模。区津保雲都た模区ょ知以離無夜いへてつ舳瀬っリシトサコル尾ゅ、く遊区あねぬ譜個ヤメモケんほなゆにり舳氏サソヤメスたか根他根絵露個、さ手御シンヒ尾瀬おすっっ阿等差そらやはのえもゆぬつ留模手雲ょいけあ留差おたぬろう樹屋ももゅつふえレッネツにすつはむ「知他知列ユ」ハスーロヒひものん素みこはゆふぬ列留樹課るゃか夜以ろ列列名擢。

たていつにに。よな個魔舳りのゅおふらら模擢の尾毛名課へろこむ手二手、譜つ素以おに差御課樹ぬスラオレコ野根なめゃ野氏。お名課無等津、ふっめゃせう屋「離屋。」はよそき保模尾阿都差かけちやゃつ区二屋魔擢尾しょとょ知手等氏れ無巣根御ゅろ以擢二魔以ふぬめりあ区遊擢よよちけ。

課絵はやすん樹鵜夜手魔他屋毛列保目屋尾野か課素擢日譜目他毛雲雲離差なれた、た毛差めそ。すんロニム。留屋れこせてへみっけ手、こおやつま屋瀬ヤウナフネロ名瀬二名すいるまさ保屋譜。
EOT;


    /**
     * @return void
     */
    public function testExtractNo()
    {
        $extractor = new BasicDocumentContentExtractor(100, 100);

        $queries = [
            'saw',
            'Note',
            'sequences',
        ];

        foreach ($queries as $query) {
            $actual = $extractor->extract(
                self::SIMPLE_TEXT,
                $query,
                ThemeOptionExtractEnum::no()
            );

            $this->assertEquals('', $actual->getText());
        }

        $queries = [
            'فنلندا',
            'ويكيبيديا',
            'مسارح',
        ];

        foreach ($queries as $query) {
            $actual = $extractor->extract(
                self::ARABIC_TEXT,
                $query,
                ThemeOptionExtractEnum::no()
            );

            $this->assertEquals('', $actual->getText());
        }

        $queries = [
            'ひすう',
            '列りな',
            '留屋れこ',
        ];

        foreach ($queries as $query) {
            $actual = $extractor->extract(
                self::HIEROGLYPHIC_TEXT,
                $query,
                ThemeOptionExtractEnum::no()
            );

            $this->assertEquals('', $actual->getText());
        }
    }

    /**
     * @dataProvider startExtractProvider
     *
     * @param string  $text            Document content text.
     * @param string  $query           Search query.
     * @param integer $startExtractLen How many characters extract from beginning
     *                                 of content.
     * @param string  $expected        Expected result.
     *
     * @return void
     */
    public function testExtractStart($text, $query, $startExtractLen, $expected)
    {
        $extractor = new BasicDocumentContentExtractor($startExtractLen, random_int(0, 100));
        $actual = $extractor->extract($text, $query, ThemeOptionExtractEnum::start());

        $this->assertEquals($expected, $actual->getText());
    }

    /**
     * @return array
     */
    public function startExtractProvider()
    {
        return [
            [
                self::SIMPLE_TEXT,
                'cat',
                20,
                'Outside a character ',
            ],
            [
                self::SIMPLE_TEXT,
                'some',
                1,
                'O',
            ],
            [
                self::SIMPLE_TEXT,
                '',
                100000000,
                self::SIMPLE_TEXT,
            ],
            [
                self::SIMPLE_TEXT,
                'long query',
                0,
                '',
            ],
            [
                self::ARABIC_TEXT,
                '',
                25,
                'مع مدن يرتبط المؤلّفة, حي',
            ],
            [
                self::ARABIC_TEXT,
                '',
                1,
                'م',
            ],
            [
                self::ARABIC_TEXT,
                '',
                10000000,
                self::ARABIC_TEXT,
            ],
            [
                self::HIEROGLYPHIC_TEXT,
                '',
                25,
                '絵列個列氏。さおひまきんせふこ二「夜等鵜もぬねゃ舳',
            ],
            [
                self::HIEROGLYPHIC_TEXT,
                '',
                1,
                '絵',
            ],
            [
                self::HIEROGLYPHIC_TEXT,
                '',
                10000000,
                self::HIEROGLYPHIC_TEXT,
            ],
        ];
    }

    /**
     * @dataProvider contextExtractProvider
     *
     * @param string  $text              Document content text.
     * @param string  $query             Search query.
     * @param integer $contextExtractLen How many characters extract before and
     *                                   after search keyword.
     * @param string  $expected          Expected result.
     *
     * @return void
     */
    public function testExtractContext($text, $query, $contextExtractLen, $expected)
    {
        $extractor = new BasicDocumentContentExtractor(random_int(0, 100), $contextExtractLen);
        $actual = $extractor->extract($text, $query, ThemeOptionExtractEnum::context());

        $this->assertEquals($expected, $actual->getText());
    }

    /**
     * @return array
     */
    public function contextExtractProvider()
    {
        return [
            [
                self::SIMPLE_TEXT,
                'mode',
                5,
                'hing mode, the',
            ],
            [
                self::SIMPLE_TEXT,
                'mode',
                0,
                'mode',
            ],
            [
                self::SIMPLE_TEXT,
                'character mode',
                25,
                'Outside a character class, in the default ma',
            ],
            [
                self::SIMPLE_TEXT,
                'cat PCRE_MULTILINE dog',
                7,
                'if the PCRE_MULTILINE option',
            ],
            [
                self::SIMPLE_TEXT,
                'mode Outside PCRE_MULTILINE',
                1000000000,
                self::SIMPLE_TEXT,
            ],
            [
                self::SIMPLE_TEXT,
                'cat',
                25,
                'MULTILINE is set or not. Cat also been here.',
            ],
            [
                self::ARABIC_TEXT,
                'المؤلّفة حين',
                25,
                'مع مدن يرتبط المؤلّفة, حين تونس تحرّكت في. بعض',
            ],
            [
                self::ARABIC_TEXT,
                'المؤلّفة',
                0,
                'المؤلّفة',
            ],
            [
                self::ARABIC_TEXT,
                'المؤلّفة',
                10000000,
                self::ARABIC_TEXT,
            ],
            [
                self::HIEROGLYPHIC_TEXT,
                '派阿ヌ ノきぬ離日み さおひまき',
                25,
                '絵列個列氏。さおひまきんせふこ二「夜等鵜もぬねゃ舳カ」ラュャコ。ほゅ。る',
            ],
            [
                self::HIEROGLYPHIC_TEXT,
                'ラュャ',
                0,
                'ラュャ',
            ],
            [
                self::HIEROGLYPHIC_TEXT,
                '派阿ヌ ノきぬ離日み',
                10000000,
                self::HIEROGLYPHIC_TEXT,
            ],
        ];
    }

    /**
     * @return void
     */
    public function testExtractEmptyContent()
    {
        $extractor = new BasicDocumentContentExtractor(100, 100);

        $this->assertEquals('', $extractor->extract('', 'mode', ThemeOptionExtractEnum::no())->getText());
        $this->assertEquals('', $extractor->extract('', 'mode', ThemeOptionExtractEnum::start())->getText());
        $this->assertEquals('', $extractor->extract('', 'mode', ThemeOptionExtractEnum::context())->getText());
    }
}
