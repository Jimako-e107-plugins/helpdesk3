##################################################################### 
#####                                                          ######
#####                                                          ######
##### Helpdesk Ticketing system by © Barry Keal 2004-8        ######
##### 									                       ######
##### © Barry Keal 2003-2008                      		      ######                     
#####										                   ###### 
#####										   
#####################################################################

Helpdesk
Installation

Copy helpdesk3_menu folder to e107_plugins folder Go to plugin manager and install in the usual manner Go to helpdesk configuration and configure

NOTE This plugin is for version 0.7 of the e107CMS. It uses a number of features that only exist in this version, for example the pdf plugin and search routines.

This is important to be able to send pdfs Make the pdfout (in helpdesk3_menu) directory chmod 755 (or full read write if on Windows environment)

Configure all the settings from admin.

You will need to create

Helpdesks Tickets are assigned to helpdesks eg a hardware helpdesk or networking etc. You then assign a userclass to this helpdesk and all members of that userclass are members of that particular helpdesk.

As an example, you have a userclass called tech_HW containing two technicians. Create a helpdesk called Hardware Helpdesk and assign the userclass tech_HW to it. Now the two technicians are in that helpdesk. I use a group email address for the helpdesk and it contains those people who need to be notified of the tickets posted to hardware faults.

If you only have a small helpdesk you probably only want to create one helpdesk with all your techncians in.

Categories These are the types of fault people will be reporting eg broken monitor

Assign a helpdesk who will deal with this type of problem. Broken monitors for example will be attended to by the Hardware Helpdesk.

Status The current status of the ticket - eg open & not assigned to a helpdesk (Helpdesk supervisor has not given it to anybody yet to fix) Assigned to a helpdesk (Waiting for technicians to fix it) Closed and Fixed (the problem has been fixed) This closes the ticket Auto Closed (We were waiting for the user to close the ticket when it has been fixed but they didn't respond in time so the system auto closed it assuming it to have been fixed. This closes the ticket. Invalid Not a helpdesk problem (This closes the ticket)

Fixes If you have standard fixes, if on a term contract for example, create them next. You might have some standard fixes like Replaced Monitor or Reset Password. If using the financial reporting you can add a standard cost in too.

Colours Set the colours for the various priorities.

More info below.

Then you will need to configure the main config page based on the information created above. You will also need to go and set up the mail settings with the text you want in various emails.



Features:

Users can post a help desk ticket and track its progress.

Admin can set escalation period in days eg after 7 days yellow after another 7 days it goes red

System can autoclose tickets after x days

Filter on open/closed/assigned/not assigned/escalate

Post comments from user/helpdesk

Print a copy of the ticket (produces pdf.) This does NOT require pdf libraries on your server as it uses fpdf from www.fpdf.org. It does require fpdf directory to be put in plugins directory (not in the helpdesk3_menu directory.

Basic report generation for Admin and helpdesk supervisor (produces pdf output)



Customising for your site

The list of categories can be changed to suit your requirements. Change the definitions from the admin menu.

The logo that appears on the top of the printable ticket is in images and called logo_hd.png You can change it (keep the same h & V size though)

As there may be some confusion for some people over my use of terminology I’ve attempted to give a brief synopsis of each of the different areas of the helpdesk’s operation. It is not yet very comprehensive I’m afraid.

What is a Helpdesk? In the context of this program a helpdesk has a name and a user class assigned to it. That userclass may have just one member or many members. For example you may want to have 3 helpdesks that deal with Network issues, Software issues and Hardware faults. The first has one member, the second 6 members and the last one 3 members.

To do this create your three userclasses, eg help_net, help_soft and help_hard. Assign technicians to the appropriate user class and remember they can be members of more than one class. A multi skilled technician may be in both hardware and networks for example.

Now create your three helpdesks and assign the user classes as appropriate (by that I mean the network class to the networking helpdesk) and so on through all your list. You should also assign an email address that either belongs to one of the technicians who will be responsible for the job or a distribution list for all the technicians at that helpdesk.

If you want to assign directly to technicians, maybe because you only have a small helpdesk, then just create a helpdesk with that technician’s name, make them the sole member of the helpdesk userclass and specify his/her email address.

This is you helpdesk list sorted.

What are categories? These are areas of faults or, if you want to make it very specific, all the faults you may have to deal with. Taking an IT helpdesk as an example you might want such categories as

Logging on problems Monitor faults Microsoft Office Problems Accounts Package Problems Email Problems

Each category of fault can have a helpdesk assigned to it so that if the auto assign option is turned on when a ticket is created it will automatically be assigned to the appropriate helpdesk. That helpdesk will then be emailed when a new ticket is created or modified.

What is a Status? The status gives some feedback to users and people viewing your helpdesk tickets. For example you may have the following Accepted on to the system. This means that the ticket has been created but nobody is looking at it yet. Assigned The ticket has now been assigned to a helpdesk to be resolved. Closed The ticket has been closed because the problem has been fixed. Closed - Automatic close The system closed the ticket because the client failed to supply additional information that had been requested. Waiting for parts. Obviously… waiting for parts.

Standard Fixes If you have standard fixes that can be applied you can define those. Examples may be reissue password or replace monitor. If you are using the financial information you can supply a cost of the fix which will be inserted into the ticket details automatically if the fix is selected.



Version History

Version 3 Major rewrite. You can not upgrade from version 2, too many changes.

Version 3.01

Now menu option to define colours for priority, status, standard fixes, mail settings Financial information on each ticket. Can attach a pdf of the ticket with email Turn off certain fields

Comments:

From the plugin config

Set which classes can view/post tickets (I use public or members only). Public means no username or email address guarantees when they submit the ticket, ie can be posted anonymously.
Set which user class is the helpdesk supervisor (currently doesn't do much though I intend to have reporting for supervisors in a later version)
Set which user class contains the technicians these can open, close, edit, and quick entry tickets
Set visibility of menu box if required (shows total tickets, open and not assigned). If supervisor, admin or technician there is a "quick entry" link and one for "quick assign" from the menu that are only visible to technicians supervisor and admin.
