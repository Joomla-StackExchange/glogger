# glogger: Joomla Global Logging
<p>gLogger started as a simple class to extend the standard JLog class so that the logging information was saved in a table rather than in text files scattered around the \logs folder.  Initially, it just stored standard data so that I could view in a single place.  I quickly realized that all sorts of other pertinant data was available, and that may other functions could be incorporated.
</p>

<p>Here's my initial offering.  It started as a Class to extend JLog, and then a quick UI to see the data, then some methods, then some of this, and some of that to see if....  Long story short, it was never "designed", it's been a series of tweaks and experiments to see how usable I can make it.  If there is any serious interest in using it, I'll likely refactor some things and clean up the code - please don't judge me based on the guts of this thing, lol.</p>

<p><strong>Main gLogger Listing View</strong> Allows sorting/filtering/selelection of all gLog records in the database.
  <img src="screenshots/01_glogs.png" width="100%"/>
</p>

<p><strong>gLog Details</strong> The basic information about the gLogger record, and the Plain Text consolition of it's entries
  <img src="screenshots/02_glog.png" width="100%"/>
</p>

<p><strong>gLog Entries</strong> Each gLog Entry shown wtih jsTree, including the full backtrace of each call
  <img src="screenshots/03_glog.png" width="100%"/>
</p>

<p><strong>Data Elements Listing View</strong>  If you add any data to the gLogger using <code>->addData()</code>, then it each is displayed here
  <img src="screenshots/04_glog.png" width="100%"/>
</p>

<p><strong>gLogger Object</strong> The full gLogger object displayed using <code>print_r()</code>
  <img src="screenshots/05_glog.png" width="100%"/>
</p>

<p><strong>External Window for viewing Large Objects</strong> If there are a lot of Log Entries, or large/complex data items, this window can be opened to view them in their entirety using <code>print_r()</code>
  <img src="screenshots/06_glog.png" width="100%"/>
</p>

<p><strong>gLogger Configuration Options</strong> Standard Joomla Component Options
  <img src="screenshots/07_options.png" width="100%"/>
</p>

<p><strong>Component Documentation - Data and Methods</strong> Documentation of gLogger Data and Methods (<em>very much a work in progress</em>)
  <img src="screenshots/08_options.png" width="100%"/>
</p>

<p><strong>Component Documentation - Uses and Helper Function</strong> Documentation of gLogger Uses and Helper Function (<em>very much a work in progress</em>
  <img src="screenshots/09_options.png" width="100%"/>
</p>

<p><strong>Examples PHP page</strong> Working PHP script and links using the gLogger class that shows the code used and the resulting Logss and database entries  (<em>very much a work in progress</em>
  <img src="screenshots/10_examples.png" width="100%"/>
</p>
