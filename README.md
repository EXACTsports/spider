![Chipper CI](https://app.chipperci.com/projects/f1b67b90-787b-48e1-8107-17c06c2dc03e/status/master)
[![StyleCI](https://github.styleci.io/repos/200163522/shield?branch=master)](https://github.styleci.io/repos/200163522)
# Directory Spider
Crawl college directories regularly via AWS Lambda, populate a database of contacts, and provide  a standardized API for our other sites to use to keep their coaching staff records up to date. It works by fetching a copy of the directory and passing the resulting HTML through extractor classes to collect contact information and classify contacts as coaches.
### Creating A New Extractor
A directory extractor is a class that we pass the HTML of a directory or page to in hopes that it will extract the contact information from the page and allow us to classify the contacts as coaches (identifying team, title, etc.). Every scraped directory page is passed through all extractors and we use the one that gives the best results (most correctly identified and classified coaches). To get as close to 100% automation of our master college coach directory as possible, we have made it easy to add new extractors to the process.

To create a new extractor, run this command: `php artisan make:extractor` and answer the three questions you are presented with:
1. The name of the college whose directory you are working with.
2. The URL of the directory or profile page you are working with.
3. Whether the page you are working with is a profile page.

This will result in the creation of three files:

`App/Actions/Extractors/{ExtractorName}.php` - The extractor class stubbed out with expected parameters and return.

`tests/Unit/{ExtractorName}Test.php` - A test class with the tests stubbed out. They should fail when you run phpunit until you compete the logic in the extractor class.

`storage/test_pages/{ExtractorName}.html` - A blank file into which you will paste the source of a directory page that will be parsed using the extractor that you are creating.

This system also creates a new git branch at {ExtractorName} (after pulling the master branch down to make sure you're up to date), checks it out, and pushes it to remote.

After you paste the source of the page you want to extract into the test directories file created during the creation process, the process is pretty straightforward:

1. Examine the contents of the directory page you are trying to extract and write assertions in the test to match the happy path (sees a particular contact with correct information in the resulting array, for instance).
2. Run the test to ensure it fails.
3. Write the logic into the __invoke() function required to extract the contact data from the source of the test page.
4. Ensure the test passes.
5. Refactor the extractor to ensure the code is readable (no deep recursions, etc.) and keep running the test to make sure it passes.
6. When you are confident that you have a working extractor push your work to remote. If it passes StyleCI and ChipperCI, click the button to submit a pull request. Otherwise, make the fixes required to get it to pass all CI checks and then submit the pull request.

#### Requirements for an Extractor
These are the fields we want to gather from the directory for each coach:
<table>
    <tr>
        <th>Name</th>
        <td>name</td>
        <td>Full name (first and last) max 128</td>
        <td>Required</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>email</td>
        <td>Trimmed, lowercase, valid email address</td>
        <td>Required</td>
    </tr>
    <tr>
        <th>Phone</th>
        <td>phone</td>
        <td>10 digit numeric string (no punctuation)</td>
        <td>Strongly Desired</td>
    </tr>
    <tr>
        <th>Title</th>
        <td>title</td>
        <td>Head Coach, Assistant Coach, etc.</td>
        <td>Strongly Desired</td>
    </tr>
    <tr>
        <th>Profile URL</th>
        <td>profile_url</td>
        <td>URI relative to the directory URL or FQURL</td>
        <td>Desired</td>
    </tr>
    <tr>
        <th>Picture URL</th>
        <td>picture_url</td>
        <td>URI relative to the directory URL or FQURL</td>
        <td>Desired</td>
    </tr>
    <tr>
        <th>Biography</th>
        <td>bio</td>
        <td>Test (html of bio passed through App\Actions\CleanBio)</td>
        <td>Desired</td
    </tr>
</table>
I will not merge a pull request that does not extract all the data possible from the test file. In other words, the only reason I will accept an extraction that does not gather phone numbers is that there are no phone numbers on the directory page. A profile extractor, on the other hand, will be merged in if it provides any data that its corresponding directory does not. If a directory does not provide a required piece of information but linked profile pages do, your pull request must contain both an extractor for the directory and the profile page so that a full data set for the coaches can be built.

### Consuming the API

This is the pattern for querying the API. This is not an open api and I will, for now, add the required authentication to any sites that will consume the API. 

`https://directory-spider.exactsports.com/{unitid}` - All coaches at a school

`htts://directory-spider.exactsports.com/{unitid}/?sport=soccer` - All soccer coaches

`https://directory-spider.exactsports.com/{unitid}/?sport=soccer&gender=m` - All men's soccer coaches
