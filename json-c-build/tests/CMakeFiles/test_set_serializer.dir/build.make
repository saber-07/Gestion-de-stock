# CMAKE generated file: DO NOT EDIT!
# Generated by "Unix Makefiles" Generator, CMake Version 3.25

# Delete rule output on recipe failure.
.DELETE_ON_ERROR:

#=============================================================================
# Special targets provided by cmake.

# Disable implicit rules so canonical targets will work.
.SUFFIXES:

# Disable VCS-based implicit rules.
% : %,v

# Disable VCS-based implicit rules.
% : RCS/%

# Disable VCS-based implicit rules.
% : RCS/%,v

# Disable VCS-based implicit rules.
% : SCCS/s.%

# Disable VCS-based implicit rules.
% : s.%

.SUFFIXES: .hpux_make_needs_suffix_list

# Command-line flag to silence nested $(MAKE).
$(VERBOSE)MAKESILENT = -s

#Suppress display of executed commands.
$(VERBOSE).SILENT:

# A target that is always out of date.
cmake_force:
.PHONY : cmake_force

#=============================================================================
# Set environment variables for the build.

# The shell in which to execute make rules.
SHELL = /bin/sh

# The CMake executable.
CMAKE_COMMAND = /opt/homebrew/Cellar/cmake/3.25.0/bin/cmake

# The command to remove a file.
RM = /opt/homebrew/Cellar/cmake/3.25.0/bin/cmake -E rm -f

# Escaping for special characters.
EQUALS = =

# The top-level source directory on which CMake was run.
CMAKE_SOURCE_DIR = "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c"

# The top-level build directory on which CMake was run.
CMAKE_BINARY_DIR = "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build"

# Include any dependencies generated for this target.
include tests/CMakeFiles/test_set_serializer.dir/depend.make
# Include any dependencies generated by the compiler for this target.
include tests/CMakeFiles/test_set_serializer.dir/compiler_depend.make

# Include the progress variables for this target.
include tests/CMakeFiles/test_set_serializer.dir/progress.make

# Include the compile flags for this target's objects.
include tests/CMakeFiles/test_set_serializer.dir/flags.make

tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o: tests/CMakeFiles/test_set_serializer.dir/flags.make
tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o: /Users/saber07/Desktop/projet\ BD:RESEAU/GestionStock/json-c/tests/test_set_serializer.c
tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o: tests/CMakeFiles/test_set_serializer.dir/compiler_depend.ts
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green --progress-dir="/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/CMakeFiles" --progress-num=$(CMAKE_PROGRESS_1) "Building C object tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o"
	cd "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/tests" && /Library/Developer/CommandLineTools/usr/bin/cc $(C_DEFINES) $(C_INCLUDES) $(C_FLAGS) -MD -MT tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o -MF CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o.d -o CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o -c "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c/tests/test_set_serializer.c"

tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing C source to CMakeFiles/test_set_serializer.dir/test_set_serializer.c.i"
	cd "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/tests" && /Library/Developer/CommandLineTools/usr/bin/cc $(C_DEFINES) $(C_INCLUDES) $(C_FLAGS) -E "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c/tests/test_set_serializer.c" > CMakeFiles/test_set_serializer.dir/test_set_serializer.c.i

tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling C source to assembly CMakeFiles/test_set_serializer.dir/test_set_serializer.c.s"
	cd "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/tests" && /Library/Developer/CommandLineTools/usr/bin/cc $(C_DEFINES) $(C_INCLUDES) $(C_FLAGS) -S "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c/tests/test_set_serializer.c" -o CMakeFiles/test_set_serializer.dir/test_set_serializer.c.s

# Object files for target test_set_serializer
test_set_serializer_OBJECTS = \
"CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o"

# External object files for target test_set_serializer
test_set_serializer_EXTERNAL_OBJECTS =

tests/test_set_serializer: tests/CMakeFiles/test_set_serializer.dir/test_set_serializer.c.o
tests/test_set_serializer: tests/CMakeFiles/test_set_serializer.dir/build.make
tests/test_set_serializer: libjson-c.5.2.0.dylib
tests/test_set_serializer: tests/CMakeFiles/test_set_serializer.dir/link.txt
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green --bold --progress-dir="/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/CMakeFiles" --progress-num=$(CMAKE_PROGRESS_2) "Linking C executable test_set_serializer"
	cd "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/tests" && $(CMAKE_COMMAND) -E cmake_link_script CMakeFiles/test_set_serializer.dir/link.txt --verbose=$(VERBOSE)

# Rule to build all files generated by this target.
tests/CMakeFiles/test_set_serializer.dir/build: tests/test_set_serializer
.PHONY : tests/CMakeFiles/test_set_serializer.dir/build

tests/CMakeFiles/test_set_serializer.dir/clean:
	cd "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/tests" && $(CMAKE_COMMAND) -P CMakeFiles/test_set_serializer.dir/cmake_clean.cmake
.PHONY : tests/CMakeFiles/test_set_serializer.dir/clean

tests/CMakeFiles/test_set_serializer.dir/depend:
	cd "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build" && $(CMAKE_COMMAND) -E cmake_depends "Unix Makefiles" "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c" "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c/tests" "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build" "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/tests" "/Users/saber07/Desktop/projet BD:RESEAU/GestionStock/json-c-build/tests/CMakeFiles/test_set_serializer.dir/DependInfo.cmake" --color=$(COLOR)
.PHONY : tests/CMakeFiles/test_set_serializer.dir/depend

